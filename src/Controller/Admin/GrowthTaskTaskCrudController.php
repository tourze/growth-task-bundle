<?php

namespace GrowthTaskBundle\Controller\Admin;

use GrowthTaskBundle\Entity\Task;
use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;

class GrowthTaskTaskCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Task::class;
    }
}
