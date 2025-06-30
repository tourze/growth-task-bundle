<?php

namespace GrowthTaskBundle\Tests\Unit\Procedure;

use GrowthTaskBundle\Entity\Grouping;
use GrowthTaskBundle\Entity\Task;
use GrowthTaskBundle\Procedure\GetGrowthTaskList;
use GrowthTaskBundle\Repository\GroupingRepository;
use GrowthTaskBundle\Repository\RecordRepository;
use GrowthTaskBundle\Repository\TaskRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class GetGrowthTaskListTest extends TestCase
{
    private GetGrowthTaskList $procedure;
    private GroupingRepository&MockObject $groupingRepository;
    private TaskRepository&MockObject $taskRepository;
    private RecordRepository&MockObject $recordRepository;
    private Security&MockObject $security;

    protected function setUp(): void
    {
        $this->groupingRepository = $this->createMock(GroupingRepository::class);
        $this->taskRepository = $this->createMock(TaskRepository::class);
        $this->recordRepository = $this->createMock(RecordRepository::class);
        $this->security = $this->createMock(Security::class);

        $this->procedure = new GetGrowthTaskList(
            $this->groupingRepository,
            $this->taskRepository,
            $this->recordRepository,
            $this->security
        );
    }

    public function testInstantiation(): void
    {
        $this->assertInstanceOf(GetGrowthTaskList::class, $this->procedure);
    }

    public function testExecuteReturnsEmptyArrayWhenGroupNotFound(): void
    {
        $this->procedure->group = 'non-existent-group';
        
        $this->groupingRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['title' => 'non-existent-group'])
            ->willReturn(null);

        $result = $this->procedure->execute();
        
        $this->assertSame([], $result);
    }

    public function testExecuteReturnsTaskListWhenGroupExists(): void
    {
        $grouping = $this->createMock(Grouping::class);
        $task = $this->createMock(Task::class);
        $user = $this->createMock(UserInterface::class);
        
        $this->procedure->group = 'test-group';
        
        $this->groupingRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['title' => 'test-group'])
            ->willReturn($grouping);

        $this->taskRepository
            ->expects($this->once())
            ->method('findBy')
            ->with(
                ['grouping' => $grouping, 'valid' => true],
                ['sortNumber' => 'DESC']
            )
            ->willReturn([$task]);

        $this->security
            ->expects($this->atLeastOnce())
            ->method('getUser')
            ->willReturn($user);

        $queryBuilder = $this->createMock(\Doctrine\ORM\QueryBuilder::class);
        $query = $this->createMock(\Doctrine\ORM\Query::class);
        
        $this->recordRepository
            ->expects($this->exactly(2))
            ->method('createQueryBuilder')
            ->willReturn($queryBuilder);

        $queryBuilder->method('select')->willReturnSelf();
        $queryBuilder->method('where')->willReturnSelf();
        $queryBuilder->method('setParameter')->willReturnSelf();
        $queryBuilder->method('groupBy')->willReturnSelf();
        $queryBuilder->method('andWhere')->willReturnSelf();
        $queryBuilder->method('getQuery')->willReturn($query);
        
        $query->method('getArrayResult')->willReturn([]);

        $task
            ->expects($this->once())
            ->method('retrieveApiArray')
            ->willReturn(['id' => 'task-1', 'title' => 'Test Task']);

        $result = $this->procedure->execute();
        
        $this->assertCount(1, $result);
        $this->assertArrayHasKey('totalFinishTimes', $result[0]);
        $this->assertArrayHasKey('todayFinishTimes', $result[0]);
    }
}