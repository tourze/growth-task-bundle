<?php

namespace GrowthTaskBundle\Controller\Admin;

use GrowthTaskBundle\Entity\Award;
use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;

class GrowthTaskAwardCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Award::class;
    }
}
