<?php
namespace GrowthTaskBundle\Tests\Integration\Controller\Admin;
use GrowthTaskBundle\Controller\Admin\GrowthTaskTaskCrudController;
use GrowthTaskBundle\Entity\Task;
use PHPUnit\Framework\TestCase;
class GrowthTaskTaskCrudControllerTest extends TestCase
{
    public function testGetEntityFqcn(): void
    {
        $this->assertSame(Task::class, GrowthTaskTaskCrudController::getEntityFqcn());
    }
}