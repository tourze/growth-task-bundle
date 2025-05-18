<?php

namespace GrowthTaskBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use GrowthTaskBundle\Entity\Grouping;

/**
 * @method Grouping|null find($id, $lockMode = null, $lockVersion = null)
 * @method Grouping|null findOneBy(array $criteria, array $orderBy = null)
 * @method Grouping[]    findAll()
 * @method Grouping[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GroupingRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Grouping::class);
    }
}
