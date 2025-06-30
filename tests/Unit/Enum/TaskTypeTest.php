<?php

namespace GrowthTaskBundle\Tests\Unit\Enum;

use GrowthTaskBundle\Enum\TaskType;
use PHPUnit\Framework\TestCase;

class TaskTypeTest extends TestCase
{
    public function testEnumExists(): void
    {
        $this->assertTrue(enum_exists(TaskType::class));
    }

    public function testCases(): void
    {
        $cases = TaskType::cases();
        $this->assertGreaterThan(0, count($cases));
    }
}