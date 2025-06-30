<?php

namespace GrowthTaskBundle\Tests\Integration\EventSubscriber;

use GrowthTaskBundle\EventSubscriber\ChanceSubscriber;
use PHPUnit\Framework\TestCase;

class ChanceSubscriberTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(ChanceSubscriber::class));
    }
}