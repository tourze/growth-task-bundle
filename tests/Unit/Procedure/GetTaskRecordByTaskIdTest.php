<?php

namespace GrowthTaskBundle\Tests\Unit\Procedure;

use GrowthTaskBundle\Entity\Record;
use GrowthTaskBundle\Entity\Task;
use GrowthTaskBundle\Procedure\GetTaskRecordByTaskId;
use GrowthTaskBundle\Repository\RecordRepository;
use GrowthTaskBundle\Repository\TaskRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\JsonRPC\Core\Exception\ApiException;

class GetTaskRecordByTaskIdTest extends TestCase
{
    private GetTaskRecordByTaskId $procedure;
    private RecordRepository&MockObject $recordRepository;
    private Security&MockObject $security;
    private TaskRepository&MockObject $taskRepository;

    protected function setUp(): void
    {
        $this->recordRepository = $this->createMock(RecordRepository::class);
        $this->security = $this->createMock(Security::class);
        $this->taskRepository = $this->createMock(TaskRepository::class);

        $this->procedure = new GetTaskRecordByTaskId(
            $this->recordRepository,
            $this->security,
            $this->taskRepository
        );
    }

    public function testInstantiation(): void
    {
        $this->assertInstanceOf(GetTaskRecordByTaskId::class, $this->procedure);
    }

    public function testExecuteThrowsExceptionWhenTaskNotFound(): void
    {
        $this->procedure->taskId = 'non-existent-task';
        
        $this->taskRepository
            ->expects($this->once())
            ->method('find')
            ->with('non-existent-task')
            ->willReturn(null);

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('任务不存在');
        
        $this->procedure->execute();
    }

    public function testExecuteReturnsRecordList(): void
    {
        $task = $this->createMock(Task::class);
        $user = $this->createMock(UserInterface::class);
        $record = $this->createMock(Record::class);
        
        $this->procedure->taskId = 'task-123';
        
        $this->taskRepository
            ->expects($this->once())
            ->method('find')
            ->with('task-123')
            ->willReturn($task);

        $this->security
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($user);

        $this->recordRepository
            ->expects($this->once())
            ->method('findBy')
            ->with([
                'task' => $task,
                'user' => $user,
            ])
            ->willReturn([$record]);

        $record
            ->expects($this->once())
            ->method('retrievePlainArray')
            ->willReturn(['id' => 'record-1', 'task' => 'task-123']);

        $result = $this->procedure->execute();
        
        $this->assertCount(1, $result);
        $this->assertEquals(['id' => 'record-1', 'task' => 'task-123'], $result[0]);
    }

    public function testExecuteReturnsEmptyArrayWhenNoRecords(): void
    {
        $task = $this->createMock(Task::class);
        $user = $this->createMock(UserInterface::class);
        
        $this->procedure->taskId = 'task-123';
        
        $this->taskRepository
            ->expects($this->once())
            ->method('find')
            ->with('task-123')
            ->willReturn($task);

        $this->security
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($user);

        $this->recordRepository
            ->expects($this->once())
            ->method('findBy')
            ->with([
                'task' => $task,
                'user' => $user,
            ])
            ->willReturn([]);

        $result = $this->procedure->execute();
        
        $this->assertEmpty($result);
    }
}