<?php

namespace GrowthTaskBundle\Tests\Unit\Event;

use GrowthTaskBundle\Event\BeforeSaveTaskRecordEvent;
use PHPUnit\Framework\TestCase;

class BeforeSaveTaskRecordEventTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(BeforeSaveTaskRecordEvent::class));
    }
}