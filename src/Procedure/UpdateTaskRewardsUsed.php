<?php

namespace GrowthTaskBundle\Procedure;

use GrowthTaskBundle\Repository\RecordRepository;
use GrowthTaskBundle\Repository\RewardRepository;
use GrowthTaskBundle\Repository\TaskRepository;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;
use Tourze\JsonRPCLogBundle\Attribute\Log;

#[MethodTag(name: '任务模块')]
#[MethodDoc(summary: '标记任务奖励完成')]
#[IsGranted(attribute: 'IS_AUTHENTICATED_FULLY')]
#[MethodExpose(method: 'UpdateTaskRewardsUsed')]
#[Log]
#[WithMonologChannel(channel: 'procedure')]
class UpdateTaskRewardsUsed extends LockableProcedure
{
    #[MethodParam(description: '任务ID')]
    public string $taskId;

    public function __construct(
        private readonly TaskRepository $taskRepository,
        private readonly RewardRepository $rewardRepository,
        private readonly RecordRepository $recordRepository,
        private readonly Security $security,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function execute(): array
    {
        $task = $this->taskRepository->findOneBy([
            'id' => $this->taskId,
            'valid' => 1,
        ]);

        if (empty($task)) {
            throw new ApiException('更新失败，任务不存在');
        }

        $records = $this->recordRepository->findOneBy([
            'task' => $task,
            'user' => $this->security->getUser(),
        ]);
        if (empty($records)) {
            return [];
        }

        try {
            $this->rewardRepository->createQueryBuilder('w')
                ->update()
                ->where('w.record in (:record)')
                ->set('w.used', ':used')
                ->setParameter('record', $records)
                ->setParameter('used', false)
                ->getQuery()
                ->execute();
        } catch (\Throwable $exception) {
            $this->logger->error('更新任务奖励完成失败', [
                'e' => $exception,
            ]);
        }

        return [
            '__showToast' => '更新成功',
        ];
    }
}
