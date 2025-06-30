<?php
namespace GrowthTaskBundle\Tests\Integration\Controller\Admin;
use GrowthTaskBundle\Controller\Admin\GrowthTaskAwardCrudController;
use GrowthTaskBundle\Entity\Award;
use PHPUnit\Framework\TestCase;
class GrowthTaskAwardCrudControllerTest extends TestCase
{
    public function testGetEntityFqcn(): void
    {
        $this->assertSame(Award::class, GrowthTaskAwardCrudController::getEntityFqcn());
    }
}