<?php

namespace GrowthTaskBundle\Procedure;

use Carbon\CarbonImmutable;
use GrowthTaskBundle\Repository\GroupingRepository;
use GrowthTaskBundle\Repository\RecordRepository;
use GrowthTaskBundle\Repository\TaskRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;

/**
 * 获取分组下的所有任务
 */
#[MethodExpose('GetGrowthTaskList')]
#[MethodTag('任务模块')]
#[MethodDoc('获取分组下的所有任务')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class GetGrowthTaskList extends BaseProcedure
{
    #[MethodParam('分组名')]
    public string $group = '';

    public function __construct(
        private readonly GroupingRepository $groupingRepository,
        private readonly TaskRepository $taskRepository,
        private readonly RecordRepository $recordRepository,
        private readonly Security $security,
    ) {
    }

    public function execute(): array
    {
        $group = $this->groupingRepository->findOneBy(['title' => $this->group]);
        if (empty($group)) {
            return [];
        }
        $tasks = $this->taskRepository->findBy([
            'grouping' => $group,
            'valid' => true,
        ], ['sortNumber' => 'DESC']);
        $list = [];

        $totalsTmp = $this->recordRepository->createQueryBuilder('r')
            ->select('count(r.id) as total,identity(r.task) as task')
            ->where('r.user = :user')
            ->setParameter('user', $this->security->getUser())
            ->groupBy('r.task')
            ->getQuery()
            ->getArrayResult();
        $totals = [];
        foreach ($totalsTmp as $item) {
            $totals[$item['task']] = $item['total'];
        }

        $day = CarbonImmutable::today();
        $todayTotalsTmp = $this->recordRepository->createQueryBuilder('r')
            ->select('count(r.id) as total,identity(r.task) as task')
            ->where('r.user = :user')
            ->setParameter('user', $this->security->getUser())
            ->andWhere('r.createTime between :start and :end')
            ->setParameter('start', $day->startOfDay())
            ->setParameter('end', $day->endOfDay())
            ->groupBy('r.task')
            ->getQuery()
            ->getArrayResult();
        $todayTotals = [];
        foreach ($todayTotalsTmp as $item) {
            $todayTotals[$item['task']] = $item['total'];
        }

        foreach ($tasks as $task) {
            $tmp = $task->retrieveApiArray();

            $tmp['totalFinishTimes'] = $totals[$tmp['id']] ?? 0;
            $tmp['todayFinishTimes'] = $todayTotals[$tmp['id']] ?? 0;

            $list[] = $tmp;
        }

        return $list;
    }
}
