<?php

namespace GrowthTaskBundle\Controller\Admin;

use GrowthTaskBundle\Entity\TaskAttribute;
use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;

class GrowthTaskTaskAttributeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return TaskAttribute::class;
    }
}
