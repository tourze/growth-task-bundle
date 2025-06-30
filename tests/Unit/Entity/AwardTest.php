<?php

namespace GrowthTaskBundle\Tests\Unit\Entity;

use GrowthTaskBundle\Entity\Award;
use GrowthTaskBundle\Entity\Task;
use GrowthTaskBundle\Enum\AwardType;
use GrowthTaskBundle\Enum\RangeType;
use PHPUnit\Framework\TestCase;

class AwardTest extends TestCase
{
    private Award $award;

    protected function setUp(): void
    {
        $this->award = new Award();
    }

    public function testGetSetTask(): void
    {
        $task = $this->createMock(Task::class);
        
        $this->award->setTask($task);
        $this->assertSame($task, $this->award->getTask());
    }

    public function testGetSetTimes(): void
    {
        $this->award->setTimes(5);
        $this->assertSame(5, $this->award->getTimes());
    }

    public function testGetSetType(): void
    {
        $this->award->setType(AwardType::CREDIT);
        $this->assertSame(AwardType::CREDIT, $this->award->getType());
    }

    public function testGetSetValue(): void
    {
        $this->award->setValue('100');
        $this->assertSame('100', $this->award->getValue());
    }

    public function testGetSetRangeType(): void
    {
        $this->award->setRangeType(RangeType::EQUALS);
        $this->assertSame(RangeType::EQUALS, $this->award->getRangeType());
    }
}