<?php

namespace GrowthTaskBundle\Tests\Integration\Repository;

use Doctrine\Persistence\ManagerRegistry;
use GrowthTaskBundle\Entity\Grouping;
use GrowthTaskBundle\Repository\GroupingRepository;
use PHPUnit\Framework\TestCase;

class GroupingRepositoryTest extends TestCase
{
    private GroupingRepository $repository;
    private ManagerRegistry $registry;

    protected function setUp(): void
    {
        $this->registry = $this->createMock(ManagerRegistry::class);
        $this->repository = new GroupingRepository($this->registry);
    }

    public function testConstruct(): void
    {
        $this->assertInstanceOf(GroupingRepository::class, $this->repository);
    }

    public function testEntityClass(): void
    {
        // 简单验证实体类名正确
        $this->assertTrue(class_exists(Grouping::class));
    }
}