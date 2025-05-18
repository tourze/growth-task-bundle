<?php

namespace GrowthTaskBundle\Controller\Admin;

use GrowthTaskBundle\Entity\TaskType;
use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;

class GrowthTaskTaskTypeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return TaskType::class;
    }
}
