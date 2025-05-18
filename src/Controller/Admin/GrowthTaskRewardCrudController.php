<?php

namespace GrowthTaskBundle\Controller\Admin;

use GrowthTaskBundle\Entity\Reward;
use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;

class GrowthTaskRewardCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Reward::class;
    }
}
