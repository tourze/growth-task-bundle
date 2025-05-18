<?php

namespace GrowthTaskBundle\Controller\Admin;

use GrowthTaskBundle\Entity\Grouping;
use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;

class GrowthTaskGroupingCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Grouping::class;
    }
}
