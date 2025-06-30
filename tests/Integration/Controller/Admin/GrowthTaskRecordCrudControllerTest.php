<?php
namespace GrowthTaskBundle\Tests\Integration\Controller\Admin;
use GrowthTaskBundle\Controller\Admin\GrowthTaskRecordCrudController;
use GrowthTaskBundle\Entity\Record;
use PHPUnit\Framework\TestCase;
class GrowthTaskRecordCrudControllerTest extends TestCase
{
    public function testGetEntityFqcn(): void
    {
        $this->assertSame(Record::class, GrowthTaskRecordCrudController::getEntityFqcn());
    }
}