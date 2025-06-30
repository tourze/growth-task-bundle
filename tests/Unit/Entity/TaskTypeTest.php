<?php

namespace GrowthTaskBundle\Tests\Unit\Entity;

use GrowthTaskBundle\Entity\TaskType;
use PHPUnit\Framework\TestCase;

class TaskTypeTest extends TestCase
{
    private TaskType $entity;

    protected function setUp(): void
    {
        $this->entity = new TaskType();
    }

    public function testInstantiation(): void
    {
        $this->assertInstanceOf(TaskType::class, $this->entity);
    }

    public function testGetId(): void
    {
        // 新实体的ID可能为null或0
        $id = $this->entity->getId();
        $this->assertTrue($id === null || $id === 0);
    }
}