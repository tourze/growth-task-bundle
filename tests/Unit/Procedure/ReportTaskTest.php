<?php

namespace GrowthTaskBundle\Tests\Unit\Procedure;

use GrowthTaskBundle\Entity\Record;
use GrowthTaskBundle\Entity\Task;
use GrowthTaskBundle\Procedure\ReportTask;
use GrowthTaskBundle\Repository\TaskRepository;
use GrowthTaskBundle\Service\TaskService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\JsonRPC\Core\Exception\ApiException;

class ReportTaskTest extends TestCase
{
    private ReportTask $procedure;
    private TaskRepository&MockObject $taskRepository;
    private TaskService&MockObject $taskService;
    private Security&MockObject $security;

    protected function setUp(): void
    {
        $this->taskRepository = $this->createMock(TaskRepository::class);
        $this->taskService = $this->createMock(TaskService::class);
        $this->security = $this->createMock(Security::class);

        $this->procedure = new ReportTask(
            $this->taskRepository,
            $this->taskService,
            $this->security
        );
    }

    public function testInstantiation(): void
    {
        $this->assertInstanceOf(ReportTask::class, $this->procedure);
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
        $this->expectExceptionMessage('请求失败');
        
        $this->procedure->execute();
    }

    public function testExecuteReturnsResultWithReward(): void
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

        $this->taskService
            ->expects($this->once())
            ->method('saveTask')
            ->with($task, $user)
            ->willReturn([
                'desc' => '获得10金币',
                'record' => $record
            ]);

        $record
            ->expects($this->once())
            ->method('retrievePlainArray')
            ->willReturn(['id' => 'record-1', 'reward' => '10金币']);

        $result = $this->procedure->execute();
        
        $this->assertEquals('获得10金币', $result['desc']);
        $this->assertTrue($result['hasAward']);
        $this->assertEquals('获得10金币', $result['__showToast']);
        $this->assertArrayHasKey('record', $result);
    }

    public function testExecuteReturnsResultWithoutReward(): void
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

        $this->taskService
            ->expects($this->once())
            ->method('saveTask')
            ->with($task, $user)
            ->willReturn([
                'desc' => ''
            ]);

        $result = $this->procedure->execute();
        
        $this->assertEquals('', $result['desc']);
        $this->assertFalse($result['hasAward']);
        $this->assertArrayNotHasKey('__showToast', $result);
        $this->assertArrayNotHasKey('record', $result);
    }
}