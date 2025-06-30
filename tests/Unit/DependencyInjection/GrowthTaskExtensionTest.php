<?php

namespace GrowthTaskBundle\Tests\Unit\DependencyInjection;

use GrowthTaskBundle\DependencyInjection\GrowthTaskExtension;
use PHPUnit\Framework\TestCase;

class GrowthTaskExtensionTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(GrowthTaskExtension::class));
    }
}