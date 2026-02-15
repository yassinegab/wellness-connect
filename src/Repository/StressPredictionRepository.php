<?php

namespace App\Repository;

use App\Entity\StressPrediction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StressPrediction>
 */
class StressPredictionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StressPrediction::class);
    }

    public function findAllForUser($user): array
    {
        return $this->createQueryBuilder('s')
            ->join('s.userWellBeingData', 'u')
            ->andWhere('u.user = :user')
            ->setParameter('user', $user)
            ->orderBy('s.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
