<?php

namespace GrowthTaskBundle\Tests\Unit;

use GrowthTaskBundle\GrowthTaskBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class GrowthTaskBundleTest extends TestCase
{
    public function testBundleInstantiation(): void
    {
        $bundle = new GrowthTaskBundle();
        $this->assertInstanceOf(Bundle::class, $bundle);
    }
}