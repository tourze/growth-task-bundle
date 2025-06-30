<?php

namespace GrowthTaskBundle\Tests\Unit\Procedure;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use GrowthTaskBundle\Entity\Grouping;
use GrowthTaskBundle\Entity\Task;
use GrowthTaskBundle\Procedure\GetTaskInfo;
use GrowthTaskBundle\Repository\GroupingRepository;
use GrowthTaskBundle\Repository\TaskRepository;
use PHPUnit\Framework\TestCase;

class GetTaskInfoTest extends TestCase
{
    private GetTaskInfo $procedure;
    private TaskRepository $taskRepository;
    private GroupingRepository $groupingRepository;

    protected function setUp(): void
    {
        $this->taskRepository = $this->createMock(TaskRepository::class);
        $this->groupingRepository = $this->createMock(GroupingRepository::class);
        $this->procedure = new GetTaskInfo($this->taskRepository, $this->groupingRepository);
    }

    public function testExecuteWithNonExistentGroup(): void
    {
        $this->procedure->group = 'non_existent_group';
        $this->procedure->page = '/some/page';

        $this->groupingRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['title' => 'non_existent_group'])
            ->willReturn(null);

        $result = $this->procedure->execute();

        $this->assertSame([], $result);
    }

    public function testExecuteWithNoTask(): void
    {
        $this->procedure->group = 'test_group';
        $this->procedure->page = '/some/page';

        $grouping = $this->createMock(Grouping::class);
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $query = $this->createMock(Query::class);

        $this->groupingRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['title' => 'test_group'])
            ->willReturn($grouping);

        $this->taskRepository->expects($this->once())
            ->method('createQueryBuilder')
            ->with('t')
            ->willReturn($queryBuilder);

        // 简化 mock 配置
        $queryBuilder->method('where')->willReturnSelf();
        $queryBuilder->method('andWhere')->willReturnSelf();
        $queryBuilder->method('setParameter')->willReturnSelf();
        $queryBuilder->method('setMaxResults')->willReturnSelf();
        $queryBuilder->method('getQuery')->willReturn($query);

        $query->expects($this->once())
            ->method('getOneOrNullResult')
            ->willReturn(null);

        $result = $this->procedure->execute();

        $this->assertSame([], $result);
    }

    public function testExecuteWithValidTask(): void
    {
        $this->procedure->group = 'test_group';
        $this->procedure->page = '/some/page';

        $grouping = $this->createMock(Grouping::class);
        $task = $this->createMock(Task::class);
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $query = $this->createMock(Query::class);

        $this->groupingRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['title' => 'test_group'])
            ->willReturn($grouping);

        $this->taskRepository->expects($this->once())
            ->method('createQueryBuilder')
            ->with('t')
            ->willReturn($queryBuilder);

        // 简化 mock 配置
        $queryBuilder->method('where')->willReturnSelf();
        $queryBuilder->method('andWhere')->willReturnSelf();
        $queryBuilder->method('setParameter')->willReturnSelf();
        $queryBuilder->method('setMaxResults')->willReturnSelf();
        $queryBuilder->method('getQuery')->willReturn($query);

        $query->expects($this->once())
            ->method('getOneOrNullResult')
            ->willReturn($task);

        $task->expects($this->once())
            ->method('retrieveApiArray')
            ->willReturn(['id' => 1, 'title' => 'Test Task']);

        $result = $this->procedure->execute();

        $this->assertSame(['id' => 1, 'title' => 'Test Task'], $result);
    }
}