<?php

namespace GrowthTaskBundle\Controller\Admin;

use GrowthTaskBundle\Entity\Record;
use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;

class GrowthTaskRecordCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Record::class;
    }
}
