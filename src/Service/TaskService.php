<?php

namespace GrowthTaskBundle\Service;

use Carbon\CarbonImmutable;
use CreditBundle\Service\AccountService;
use CreditBundle\Service\CurrencyService;
use CreditBundle\Service\TransactionService;
use Doctrine\ORM\EntityManagerInterface;
use GrowthTaskBundle\Entity\Record;
use GrowthTaskBundle\Entity\Reward;
use GrowthTaskBundle\Entity\Task;
use GrowthTaskBundle\Enum\AwardType;
use GrowthTaskBundle\Enum\TaskLimitType;
use GrowthTaskBundle\Event\BeforeSaveTaskRecordEvent;
use GrowthTaskBundle\Event\CheckTaskEvent;
use GrowthTaskBundle\Event\GetCreditPointEvent;
use GrowthTaskBundle\Repository\AwardRepository;
use GrowthTaskBundle\Repository\RecordRepository;
use GrowthTaskBundle\Repository\TaskRepository;
use GrowthTaskBundle\Repository\TaskTypeRepository;
use LotteryBundle\Entity\Chance;
use LotteryBundle\Repository\ActivityRepository;
use LotteryBundle\Service\LotteryService;
use OrderBundle\Entity\OfferChance;
use OrderBundle\Entity\OfferSku;
use ProductCoreBundle\Repository\SpuRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\SnowflakeBundle\Service\Snowflake;

#[Autoconfigure(public: true)]
class TaskService
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly RecordRepository $recordRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly TaskRepository $taskRepository,
        private readonly AwardRepository $awardRepository,
        private readonly TaskTypeRepository $taskTypeRepository,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly Snowflake $snowflake,
        private readonly ?ActivityRepository $activityRepository,
        private readonly ?TransactionService $transactionService,
        private readonly ?CurrencyService $currencyService,
        private readonly ?AccountService $accountService,
        private readonly ?LotteryService $lotteryService,
        private readonly ?SpuRepository $spuRepository,
    ) {
    }

    /**
     * 判断是否有积分任务
     *
     * @param int $times 奖励阶级变化的任务传此值，查第几次完成任务所获得的奖励
     *
     * @throws \Exception
     */
    public function doTask(string $typeStr, UserInterface $user, int $times = 1, string $pointStr = 'Z_Point'): ?string
    {
        $type = $this->taskTypeRepository->findOneBy([
            'code' => $typeStr,
        ]);
        if (empty($type)) {
            throw new \Exception("任务类型[{$typeStr}]不存在");
        }

        // 同样类型的任务可能存在多个
        $task = $this->taskRepository->findOneBy([
            'type' => $type,
            'valid' => true,
        ]);

        if ($task === null) {
            return '';
        }

        $res = $this->saveTask($task, $user, $times, $pointStr);

        return $res['desc'];
    }

    /**
     * 增加积分记录，领取奖励
     * 领取积分
     */
    public function saveTask(Task $task, UserInterface $user, int $times = 1, string $pointStr = 'Z_Point'): array
    {
        // 检查任务资格
        $event = new CheckTaskEvent();
        $event->setTask($task);
        $event->setUser($user);
        $event->setResult([]);
        $event->setReturn(true);
        $this->eventDispatcher->dispatch($event);
        if (!$event->isReturn()) {
            throw new ApiException('不符合任务资格');
        }

        $result = [];
        $desc = '';
        $result['desc'] = $desc;
        // 保存任务记录跟领取奖励
        $total = $this->checkFinishTime($task, $user);
        if ($total >= $task->getLimitTimes()) {
            $this->logger->info('任务已达上限，不再记录', [
                'user' => $user,
                'task' => $task,
            ]);

            return $result;
        }

        // 保存记录的前置处理
        $beforeEvent = new BeforeSaveTaskRecordEvent();
        $beforeEvent->setUser($user);
        $beforeEvent->setTask($task);
        $beforeEvent->setReturn(true);
        $this->eventDispatcher->dispatch($beforeEvent);
        if (!$beforeEvent->isReturn()) {
            throw new ApiException('保存失败');
        }

        $record = new Record();
        $record->setUser($user);
        $record->setTask($task);
        $this->entityManager->persist($record);
        $this->entityManager->flush();
        $this->logger->info('任务记录保存成功，准备获取奖励', [
            'user' => $user,
            'task' => $task,
        ]);

        // 判断能获得的奖励
        // 增加积分
        $awards = $this->getUserTaskAward($times, $task);
        $rewardValue = '';
        foreach ($awards as $award) {
            if (AwardType::CREDIT === $award->getType() && $this->accountService !== null && $this->transactionService !== null) {
                if ($award->getValue() < 0) {
                    continue;
                }
                // 不同系统积分类型不同
                $creditPointEvent = new GetCreditPointEvent();
                $creditPointEvent->setPointStr($pointStr);
                $this->eventDispatcher->dispatch($creditPointEvent);
                $pointStr = $creditPointEvent->getPointStr();

                // 给积分
                $point = $this->currencyService->getCurrencyByCode($pointStr);
                $inAccount = $this->accountService->getAccountByUser($user, $point);

                $this->transactionService->asyncIncrease(
                    'TASK' . $task->getId() . '-' . $this->snowflake->id(),
                    $inAccount,
                    $award->getValue(),
                    $task->getTitle(),
                    relationModel: Task::class,
                    relationId: $task->getId(),
                );

                $desc = $rewardValue = $award->getValue();
            }

            if (AwardType::LOTTERY === $award->getType() && $this->activityRepository !== null) {
                // 给抽奖机会，格式是 抽奖活动id | 抽奖次数
                $lotteryId = $award->getValue();
                $lotteryTime = 1;
                if (str_contains((string) $lotteryId, '|')) {
                    $tmp = explode('|', (string) $lotteryId);
                    $lotteryId = $tmp[0];
                    $lotteryTime = intval($tmp[1]);
                }

                while ($lotteryTime > 0) {
                    $chance = new Chance();
                    $chance->setActivity($this->activityRepository->find($lotteryId));
                    $chance->setValid(true);
                    $chance->setStartTime(CarbonImmutable::now());
                    // 结束时间判断
                    switch ($task->getLimitType()) {
                        case TaskLimitType::DAY:
                            $chance->setExpireTime(CarbonImmutable::now()->endOfDay());
                            break;
                        case TaskLimitType::MONTH:
                            $chance->setExpireTime(CarbonImmutable::now()->endOfMonth());
                            break;
                        case TaskLimitType::YEAR:
                            $chance->setExpireTime(CarbonImmutable::now()->endOfYear());
                            break;
                        case TaskLimitType::ACTIVITY_TIME:
                            $chance->setExpireTime($task->getEndTime());
                            break;
                    }

                    $chance->setTitle($task->getTitle());
                    // 奖池的决定，可以留给LotteryBundle内部自己做
                    $this->lotteryService->giveChance($record->getUser(), $chance);
                    --$lotteryTime;
                    $rewardValue .= $chance->getId();
                }

                $desc = '获得抽奖机会';
            }

            if (AwardType::MATERIAL === $award->getType()) {
                $spu = $this->spuRepository->find($award->getValue());
                if (empty($spu) || empty($spu->getSkus())) {
                    throw new \Exception('获取奖品失败');
                }

                $offerChance = new OfferChance();
                $offerChance->setTitle("完成任务获得SKU资格[{$award->getValue()}]");
                $offerChance->setUser($user);
                $offerChance->setStartTime(CarbonImmutable::now());
                $offerChance->setEndTime(CarbonImmutable::now()->subYear());
                $offerChance->setValid(true);

                foreach ($spu->getSkus() as $sku) {
                    $offerSku = new OfferSku();
                    $offerSku->setChance($offerChance);
                    $offerSku->setSku($sku);
                    $offerSku->setQuantity(1);
                    $offerChance->addSku($offerSku);
                }

                $this->entityManager->persist($offerChance);
                $this->entityManager->flush();
                $desc = $rewardValue = $offerChance->getId();
            }

            $reward = new Reward();
            $reward->setUser($user);
            $reward->setRecord($record);
            $reward->setAward($award);
            $reward->setValue($rewardValue);
            $this->entityManager->persist($reward);
            $this->entityManager->flush();
        }

        $result['record'] = $record;
        $result['desc'] = $desc;

        return $result;
    }

    /**
     * 检查任务的完成情况
     */
    public function checkFinishTime(Task $task, UserInterface $user): int
    {
        $qb = $this->recordRepository->createQueryBuilder('r')
            ->select('count(r.id) as total')
            ->where('r.task = :task')
            ->andWhere('r.user = :user')
            ->setParameter('task', $task)
            ->setParameter('user', $user);
        switch ($task->getLimitType()) {
            case TaskLimitType::DAY:
                $qb->andWhere('r.createTime >= :createTime')
                    ->setParameter('createTime', CarbonImmutable::today());
                break;
            case TaskLimitType::MONTH:
                $qb->andWhere('r.createTime >= :start and r.createTime <= :end')
                    ->setParameter('start', CarbonImmutable::today()->startOfMonth())
                    ->setParameter('end', CarbonImmutable::today()->endOfMonth());
                break;
            case TaskLimitType::YEAR:
                $qb->andWhere('r.createTime >= :start and r.createTime <= :end')
                    ->setParameter('start', CarbonImmutable::today()->startOfYear())
                    ->setParameter('end', CarbonImmutable::today()->endOfYear());
                break;
            case TaskLimitType::ACTIVITY_TIME:
                $qb->andWhere('r.createTime >= :start and r.createTime <= :end')
                    ->setParameter('start', $task->getStartTime())
                    ->setParameter('end', $task->getEndTime());
        }
        $record = $qb->getQuery()->getResult();

        return $record[0]['total'];
    }

    protected function getUserTaskAward(int $times, Task $task)
    {
        $awards = $this->awardRepository->findBy([
            'task' => $task,
            'times' => $times,
            'rangeType' => 'equals',
        ]);

        if (empty($awards)) {
            $timesAwards = $this->awardRepository->findBy([
                'task' => $task,
                'rangeType' => 'bigger',
            ]);
            foreach ($timesAwards as $timesAward) {
                if ($times >= $timesAward->getTimes()) {
                    $awards[] = $timesAward;
                }
            }

            $lowerTimesAwards = $this->awardRepository->findBy([
                'task' => $task,
                'rangeType' => 'lower',
            ]);
            foreach ($lowerTimesAwards as $lowerTimesAward) {
                if ($times < $lowerTimesAward->getTimes()) {
                    $awards[] = $lowerTimesAward;
                }
            }
        }

        return $awards;
    }
}
