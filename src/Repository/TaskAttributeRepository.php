<?php

namespace GrowthTaskBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use GrowthTaskBundle\Entity\TaskAttribute;

/**
 * @method TaskAttribute|null find($id, $lockMode = null, $lockVersion = null)
 * @method TaskAttribute|null findOneBy(array $criteria, array $orderBy = null)
 * @method TaskAttribute[]    findAll()
 * @method TaskAttribute[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskAttributeRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TaskAttribute::class);
    }
}
