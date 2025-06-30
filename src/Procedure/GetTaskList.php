<?php

namespace GrowthTaskBundle\Procedure;

use Carbon\CarbonImmutable;
use GrowthTaskBundle\Repository\GroupingRepository;
use GrowthTaskBundle\Repository\RecordRepository;
use GrowthTaskBundle\Repository\TaskRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;

#[MethodExpose(method: 'GetTaskList')]
#[MethodTag(name: '任务模块')]
#[MethodDoc(summary: '获取分组下的所有任务')]
class GetTaskList extends BaseProcedure
{
    #[MethodParam(description: '分组名')]
    public string $group;

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
            $tmp['finishTimes'] = $todayTotals[$tmp['id']] ?? 0;

            // 跳不同小程序的处理
            if ($tmp['redirectUrl'] && strstr((string) $tmp['redirectUrl'], 'appId')) {
                $arr = parse_url((string) $tmp['redirectUrl']);
                parse_str($arr['query'], $query);
                foreach ($query as $k => $v) {
                    if ('appId' === $k) {
                        $tmp['__navigateToMiniProgram'] = [
                            'appId' => $v,
                            'path' => "{$arr['path']}?{$arr['query']}",
                        ];
                    }

                    if (isset($query['envVersion'])) {
                        $tmp['__navigateToMiniProgram']['envVersion'] = $query['envVersion'];
                    }
                }
            }

            $list[] = $tmp;
        }

        return $list;
    }
}
