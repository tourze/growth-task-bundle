<?php

namespace GrowthTaskBundle\Tests\Unit\Enum;

use GrowthTaskBundle\Enum\AwardType;
use PHPUnit\Framework\TestCase;

class AwardTypeTest extends TestCase
{
    public function testEnumExists(): void
    {
        $this->assertTrue(enum_exists(AwardType::class));
    }

    public function testCases(): void
    {
        $cases = AwardType::cases();
        $this->assertGreaterThan(0, count($cases));
    }
}