<?php

namespace GrowthTaskBundle\Tests\Integration\Repository;

use Doctrine\Persistence\ManagerRegistry;
use GrowthTaskBundle\Entity\Record;
use GrowthTaskBundle\Repository\RecordRepository;
use PHPUnit\Framework\TestCase;

class RecordRepositoryTest extends TestCase
{
    private RecordRepository $repository;
    private ManagerRegistry $registry;

    protected function setUp(): void
    {
        $this->registry = $this->createMock(ManagerRegistry::class);
        $this->repository = new RecordRepository($this->registry);
    }

    public function testConstruct(): void
    {
        $this->assertInstanceOf(RecordRepository::class, $this->repository);
    }

    public function testEntityClass(): void
    {
        // 简单验证实体类名正确
        $this->assertTrue(class_exists(Record::class));
    }
}