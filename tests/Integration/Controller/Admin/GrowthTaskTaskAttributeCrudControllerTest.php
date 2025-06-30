<?php
namespace GrowthTaskBundle\Tests\Integration\Controller\Admin;
use GrowthTaskBundle\Controller\Admin\GrowthTaskTaskAttributeCrudController;
use GrowthTaskBundle\Entity\TaskAttribute;
use PHPUnit\Framework\TestCase;
class GrowthTaskTaskAttributeCrudControllerTest extends TestCase
{
    public function testGetEntityFqcn(): void
    {
        $this->assertSame(TaskAttribute::class, GrowthTaskTaskAttributeCrudController::getEntityFqcn());
    }
}