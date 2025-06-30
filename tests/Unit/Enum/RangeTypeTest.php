<?php

namespace GrowthTaskBundle\Tests\Unit\Enum;

use GrowthTaskBundle\Enum\RangeType;
use PHPUnit\Framework\TestCase;

class RangeTypeTest extends TestCase
{
    public function testEnumExists(): void
    {
        $this->assertTrue(enum_exists(RangeType::class));
    }

    public function testCases(): void
    {
        $cases = RangeType::cases();
        $this->assertGreaterThan(0, count($cases));
    }
}