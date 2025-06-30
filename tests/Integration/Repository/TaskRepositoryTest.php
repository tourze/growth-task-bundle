<?php

namespace GrowthTaskBundle\Tests\Integration\Repository;

use Doctrine\Persistence\ManagerRegistry;
use GrowthTaskBundle\Entity\Task;
use GrowthTaskBundle\Repository\TaskRepository;
use PHPUnit\Framework\TestCase;

class TaskRepositoryTest extends TestCase
{
    private TaskRepository $repository;
    private ManagerRegistry $registry;

    protected function setUp(): void
    {
        $this->registry = $this->createMock(ManagerRegistry::class);
        $this->repository = new TaskRepository($this->registry);
    }

    public function testConstruct(): void
    {
        $this->assertInstanceOf(TaskRepository::class, $this->repository);
    }

    public function testEntityClass(): void
    {
        // 简单验证实体类名正确
        $this->assertTrue(class_exists(Task::class));
    }
}