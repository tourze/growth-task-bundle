<?php

namespace GrowthTaskBundle\Tests\Unit\Procedure;

use GrowthTaskBundle\Entity\Record;
use GrowthTaskBundle\Entity\Task;
use GrowthTaskBundle\Procedure\UpdateTaskRewardsUsed;
use GrowthTaskBundle\Repository\RecordRepository;
use GrowthTaskBundle\Repository\RewardRepository;
use GrowthTaskBundle\Repository\TaskRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\JsonRPC\Core\Exception\ApiException;

class UpdateTaskRewardsUsedTest extends TestCase
{
    private UpdateTaskRewardsUsed $procedure;
    private TaskRepository&MockObject $taskRepository;
    private RewardRepository&MockObject $rewardRepository;
    private RecordRepository&MockObject $recordRepository;
    private Security&MockObject $security;
    private LoggerInterface&MockObject $logger;

    protected function setUp(): void
    {
        $this->taskRepository = $this->createMock(TaskRepository::class);
        $this->rewardRepository = $this->createMock(RewardRepository::class);
        $this->recordRepository = $this->createMock(RecordRepository::class);
        $this->security = $this->createMock(Security::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->procedure = new UpdateTaskRewardsUsed(
            $this->taskRepository,
            $this->rewardRepository,
            $this->recordRepository,
            $this->security,
            $this->logger
        );
    }

    public function testInstantiation(): void
    {
        $this->assertInstanceOf(UpdateTaskRewardsUsed::class, $this->procedure);
    }

    public function testExecuteThrowsExceptionWhenTaskNotFound(): void
    {
        $this->procedure->taskId = 'non-existent-task';
        
        $this->taskRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with([
                'id' => 'non-existent-task',
                'valid' => 1,
            ])
            ->willReturn(null);

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('更新失败，任务不存在');
        
        $this->procedure->execute();
    }

    public function testExecuteReturnsEmptyArrayWhenNoRecords(): void
    {
        $task = $this->createMock(Task::class);
        $user = $this->createMock(UserInterface::class);
        
        $this->procedure->taskId = 'task-123';
        
        $this->taskRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with([
                'id' => 'task-123',
                'valid' => 1,
            ])
            ->willReturn($task);

        $this->security
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($user);

        $this->recordRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with([
                'task' => $task,
                'user' => $user,
            ])
            ->willReturn(null);

        $result = $this->procedure->execute();
        
        $this->assertSame([], $result);
    }

    public function testExecuteUpdatesRewardsSuccessfully(): void
    {
        $task = $this->createMock(Task::class);
        $user = $this->createMock(UserInterface::class);
        $record = $this->createMock(Record::class);
        
        $this->procedure->taskId = 'task-123';
        
        $this->taskRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with([
                'id' => 'task-123',
                'valid' => 1,
            ])
            ->willReturn($task);

        $this->security
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($user);

        $this->recordRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with([
                'task' => $task,
                'user' => $user,
            ])
            ->willReturn($record);

        $queryBuilder = $this->createMock(\Doctrine\ORM\QueryBuilder::class);
        $query = $this->createMock(\Doctrine\ORM\Query::class);
        
        $this->rewardRepository
            ->expects($this->once())
            ->method('createQueryBuilder')
            ->with('w')
            ->willReturn($queryBuilder);

        $queryBuilder->method('update')->willReturnSelf();
        $queryBuilder->method('where')->willReturnSelf();
        $queryBuilder->method('set')->willReturnSelf();
        $queryBuilder->method('setParameter')->willReturnSelf();
        $queryBuilder->method('getQuery')->willReturn($query);
        
        $query
            ->expects($this->once())
            ->method('execute')
            ->willReturn(1);

        $result = $this->procedure->execute();
        
        $this->assertEquals('更新成功', $result['__showToast']);
    }

    public function testExecuteHandlesExceptionAndLogsError(): void
    {
        $task = $this->createMock(Task::class);
        $user = $this->createMock(UserInterface::class);
        $record = $this->createMock(Record::class);
        $exception = new \Exception('Database error');
        
        $this->procedure->taskId = 'task-123';
        
        $this->taskRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with([
                'id' => 'task-123',
                'valid' => 1,
            ])
            ->willReturn($task);

        $this->security
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($user);

        $this->recordRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with([
                'task' => $task,
                'user' => $user,
            ])
            ->willReturn($record);

        $queryBuilder = $this->createMock(\Doctrine\ORM\QueryBuilder::class);
        $query = $this->createMock(\Doctrine\ORM\Query::class);
        
        $this->rewardRepository
            ->expects($this->once())
            ->method('createQueryBuilder')
            ->with('w')
            ->willReturn($queryBuilder);

        $queryBuilder->method('update')->willReturnSelf();
        $queryBuilder->method('where')->willReturnSelf();
        $queryBuilder->method('set')->willReturnSelf();
        $queryBuilder->method('setParameter')->willReturnSelf();
        $queryBuilder->method('getQuery')->willReturn($query);
        
        $query
            ->expects($this->once())
            ->method('execute')
            ->willThrowException($exception);

        $this->logger
            ->expects($this->once())
            ->method('error')
            ->with('更新任务奖励完成失败', ['e' => $exception]);

        $result = $this->procedure->execute();
        
        $this->assertEquals('更新成功', $result['__showToast']);
    }
}