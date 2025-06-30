<?php
namespace GrowthTaskBundle\Tests\Integration\Controller\Admin;
use GrowthTaskBundle\Controller\Admin\GrowthTaskRewardCrudController;
use GrowthTaskBundle\Entity\Reward;
use PHPUnit\Framework\TestCase;
class GrowthTaskRewardCrudControllerTest extends TestCase
{
    public function testGetEntityFqcn(): void
    {
        $this->assertSame(Reward::class, GrowthTaskRewardCrudController::getEntityFqcn());
    }
}