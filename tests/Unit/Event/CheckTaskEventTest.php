<?php

namespace GrowthTaskBundle\Tests\Unit\Event;

use GrowthTaskBundle\Event\CheckTaskEvent;
use PHPUnit\Framework\TestCase;

class CheckTaskEventTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(CheckTaskEvent::class));
    }
}