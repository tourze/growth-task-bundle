<?php

namespace GrowthTaskBundle\Tests\Unit\Enum;

use GrowthTaskBundle\Enum\TaskLimitType;
use PHPUnit\Framework\TestCase;

class TaskLimitTypeTest extends TestCase
{
    public function testEnumExists(): void
    {
        $this->assertTrue(enum_exists(TaskLimitType::class));
    }

    public function testCases(): void
    {
        $cases = TaskLimitType::cases();
        $this->assertGreaterThan(0, count($cases));
    }
}