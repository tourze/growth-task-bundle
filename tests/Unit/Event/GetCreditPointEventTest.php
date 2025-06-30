<?php

namespace GrowthTaskBundle\Tests\Unit\Event;

use GrowthTaskBundle\Event\GetCreditPointEvent;
use PHPUnit\Framework\TestCase;

class GetCreditPointEventTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(GetCreditPointEvent::class));
    }
}