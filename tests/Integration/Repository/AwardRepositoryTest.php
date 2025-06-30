<?php

namespace GrowthTaskBundle\Tests\Integration\Repository;

use Doctrine\Persistence\ManagerRegistry;
use GrowthTaskBundle\Entity\Award;
use GrowthTaskBundle\Repository\AwardRepository;
use PHPUnit\Framework\TestCase;

class AwardRepositoryTest extends TestCase
{
    private AwardRepository $repository;
    private ManagerRegistry $registry;

    protected function setUp(): void
    {
        $this->registry = $this->createMock(ManagerRegistry::class);
        $this->repository = new AwardRepository($this->registry);
    }

    public function testConstruct(): void
    {
        $this->assertInstanceOf(AwardRepository::class, $this->repository);
    }

    public function testEntityClass(): void
    {
        // 简单验证实体类名正确
        $this->assertTrue(class_exists(Award::class));
    }
}