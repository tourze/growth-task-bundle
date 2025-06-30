<?php

namespace GrowthTaskBundle\Tests\Unit\Entity;

use GrowthTaskBundle\Entity\Task;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    private Task $entity;

    protected function setUp(): void
    {
        $this->entity = new Task();
    }

    public function testInstantiation(): void
    {
        $this->assertInstanceOf(Task::class, $this->entity);
    }

    public function testGetId(): void
    {
        $this->assertNull($this->entity->getId());
    }
}