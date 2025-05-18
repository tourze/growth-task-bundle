<?php

namespace GrowthTaskBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use GrowthTaskBundle\Entity\TaskType;

/**
 * @method TaskType|null find($id, $lockMode = null, $lockVersion = null)
 * @method TaskType|null findOneBy(array $criteria, array $orderBy = null)
 * @method TaskType[]    findAll()
 * @method TaskType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskTypeRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TaskType::class);
    }
}
