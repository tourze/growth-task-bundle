<?php

namespace GrowthTaskBundle\Procedure;

use GrowthTaskBundle\Repository\GroupingRepository;
use GrowthTaskBundle\Repository\TaskRepository;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;

#[MethodExpose('GetTaskInfo')]
#[MethodTag('任务模块')]
#[MethodDoc('获取某个任务')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class GetTaskInfo extends BaseProcedure
{
    #[MethodParam('页面路径')]
    public string $page;

    #[MethodParam('分组名')]
    public string $group;

    public function __construct(
        private readonly TaskRepository $taskRepository,
        private readonly GroupingRepository $groupingRepository,
    ) {
    }

    public function execute(): array
    {
        $group = $this->groupingRepository->findOneBy(['title' => $this->group]);
        if (empty($group)) {
            return [];
        }

        $qb = $this->taskRepository->createQueryBuilder('t');
        $like = $qb->expr()->like('t.redirectUrl', $qb->expr()->literal("%{$this->page}%"));
        $task = $qb->where('t.grouping = :grouping and t.valid = true')
            ->andWhere($like)
            ->setParameter('grouping', $group)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
        if (empty($task)) {
            return [];
        }

        return $task->retrieveApiArray();
    }
}
