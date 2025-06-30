<?php

namespace GrowthTaskBundle\Tests\Unit\Entity;

use GrowthTaskBundle\Entity\Reward;
use PHPUnit\Framework\TestCase;

class RewardTest extends TestCase
{
    private Reward $entity;

    protected function setUp(): void
    {
        $this->entity = new Reward();
    }

    public function testInstantiation(): void
    {
        $this->assertInstanceOf(Reward::class, $this->entity);
    }

    public function testGetId(): void
    {
        $this->assertNull($this->entity->getId());
    }
}