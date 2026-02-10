<?php

namespace App\Repository;

use App\Entity\Meal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Meal>
 */
class MealRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Meal::class);
    }
    // src/Repository/MealRepository.php

    public function findBySearchAndSort(?string $search, string $sortField, string $sortDirection)
    {
        $qb = $this->createQueryBuilder('m');

        if ($search) {
            $qb->where('m.description LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        // Validate allowed sort fields
        $allowedSort = ['id', 'description', 'createAt'];
        if (!in_array($sortField, $allowedSort)) {
            $sortField = 'id';
        }

        $direction = strtolower($sortDirection) === 'desc' ? 'DESC' : 'ASC';

        $qb->orderBy('m.' . $sortField, $direction);

        return $qb->getQuery()->getResult();
    }



    //    /**
    //     * @return Meal[] Returns an array of Meal objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Meal
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
