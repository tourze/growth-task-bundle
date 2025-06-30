<?php

namespace GrowthTaskBundle\Tests\Unit\Entity;

use GrowthTaskBundle\Entity\TaskAttribute;
use PHPUnit\Framework\TestCase;

class TaskAttributeTest extends TestCase
{
    private TaskAttribute $entity;

    protected function setUp(): void
    {
        $this->entity = new TaskAttribute();
    }

    public function testInstantiation(): void
    {
        $this->assertInstanceOf(TaskAttribute::class, $this->entity);
    }

    public function testGetId(): void
    {
        // 新实体的ID可能为null或0
        $id = $this->entity->getId();
        $this->assertTrue($id === null || $id === 0);
    }
}