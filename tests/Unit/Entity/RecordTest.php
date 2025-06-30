<?php

namespace GrowthTaskBundle\Tests\Unit\Entity;

use GrowthTaskBundle\Entity\Record;
use PHPUnit\Framework\TestCase;

class RecordTest extends TestCase
{
    private Record $entity;

    protected function setUp(): void
    {
        $this->entity = new Record();
    }

    public function testInstantiation(): void
    {
        $this->assertInstanceOf(Record::class, $this->entity);
    }

    public function testGetId(): void
    {
        // 新实体的ID可能为null或0
        $id = $this->entity->getId();
        $this->assertTrue($id === null || $id === 0);
    }
}