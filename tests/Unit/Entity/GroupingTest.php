<?php

namespace GrowthTaskBundle\Tests\Unit\Entity;

use GrowthTaskBundle\Entity\Grouping;
use PHPUnit\Framework\TestCase;

class GroupingTest extends TestCase
{
    private Grouping $entity;

    protected function setUp(): void
    {
        $this->entity = new Grouping();
    }

    public function testInstantiation(): void
    {
        $this->assertInstanceOf(Grouping::class, $this->entity);
    }

    public function testGetId(): void
    {
        // 新实体的ID可能为null或0
        $id = $this->entity->getId();
        $this->assertTrue($id === null || $id === 0);
    }
}