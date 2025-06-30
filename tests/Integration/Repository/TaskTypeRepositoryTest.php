<?php

namespace GrowthTaskBundle\Tests\Integration\Repository;

use Doctrine\Persistence\ManagerRegistry;
use GrowthTaskBundle\Entity\TaskType;
use GrowthTaskBundle\Repository\TaskTypeRepository;
use PHPUnit\Framework\TestCase;

class TaskTypeRepositoryTest extends TestCase
{
    private TaskTypeRepository $repository;
    private ManagerRegistry $registry;

    protected function setUp(): void
    {
        $this->registry = $this->createMock(ManagerRegistry::class);
        $this->repository = new TaskTypeRepository($this->registry);
    }

    public function testConstruct(): void
    {
        $this->assertInstanceOf(TaskTypeRepository::class, $this->repository);
    }

    public function testEntityClass(): void
    {
        // 简单验证实体类名正确
        $this->assertTrue(class_exists(TaskType::class));
    }
}