<?php
namespace GrowthTaskBundle\Tests\Integration\Controller\Admin;
use GrowthTaskBundle\Controller\Admin\GrowthTaskTaskTypeCrudController;
use GrowthTaskBundle\Entity\TaskType;
use PHPUnit\Framework\TestCase;
class GrowthTaskTaskTypeCrudControllerTest extends TestCase
{
    public function testGetEntityFqcn(): void
    {
        $this->assertSame(TaskType::class, GrowthTaskTaskTypeCrudController::getEntityFqcn());
    }
}