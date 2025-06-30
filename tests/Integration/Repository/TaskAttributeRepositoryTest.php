<?php

namespace GrowthTaskBundle\Tests\Integration\Repository;

use Doctrine\Persistence\ManagerRegistry;
use GrowthTaskBundle\Entity\TaskAttribute;
use GrowthTaskBundle\Repository\TaskAttributeRepository;
use PHPUnit\Framework\TestCase;

class TaskAttributeRepositoryTest extends TestCase
{
    private TaskAttributeRepository $repository;
    private ManagerRegistry $registry;

    protected function setUp(): void
    {
        $this->registry = $this->createMock(ManagerRegistry::class);
        $this->repository = new TaskAttributeRepository($this->registry);
    }

    public function testConstruct(): void
    {
        $this->assertInstanceOf(TaskAttributeRepository::class, $this->repository);
    }

    public function testEntityClass(): void
    {
        // 简单验证实体类名正确
        $this->assertTrue(class_exists(TaskAttribute::class));
    }
}