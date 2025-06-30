<?php
namespace GrowthTaskBundle\Tests\Integration\Controller\Admin;
use GrowthTaskBundle\Controller\Admin\GrowthTaskGroupingCrudController;
use GrowthTaskBundle\Entity\Grouping;
use PHPUnit\Framework\TestCase;
class GrowthTaskGroupingCrudControllerTest extends TestCase
{
    public function testGetEntityFqcn(): void
    {
        $this->assertSame(Grouping::class, GrowthTaskGroupingCrudController::getEntityFqcn());
    }
}