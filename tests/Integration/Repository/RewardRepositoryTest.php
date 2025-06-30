<?php

namespace GrowthTaskBundle\Tests\Integration\Repository;

use Doctrine\Persistence\ManagerRegistry;
use GrowthTaskBundle\Entity\Reward;
use GrowthTaskBundle\Repository\RewardRepository;
use PHPUnit\Framework\TestCase;

class RewardRepositoryTest extends TestCase
{
    private RewardRepository $repository;
    private ManagerRegistry $registry;

    protected function setUp(): void
    {
        $this->registry = $this->createMock(ManagerRegistry::class);
        $this->repository = new RewardRepository($this->registry);
    }

    public function testConstruct(): void
    {
        $this->assertInstanceOf(RewardRepository::class, $this->repository);
    }

    public function testEntityClass(): void
    {
        // 简单验证实体类名正确
        $this->assertTrue(class_exists(Reward::class));
    }
}