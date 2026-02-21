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

    public function getStatistics(): array
    {
        $qb = $this->createQueryBuilder('m');
        $totalMeals = $qb->select('count(m.id)')->getQuery()->getSingleScalarResult();

        $qb = $this->createQueryBuilder('m');
        $withImage = $qb->select('count(m.id)')->where('m.imageName IS NOT NULL')->getQuery()->getSingleScalarResult();

        $qb = $this->createQueryBuilder('m');
        $withoutImage = $qb->select('count(m.id)')->where('m.imageName IS NULL')->getQuery()->getSingleScalarResult();

        $qb = $this->createQueryBuilder('m');
        try {
            // Using a simple approximation or just 0 if not supported easily
            // Note: LENGTH() is not standard DQL but works on many DBs. 
            // If it fails, we catch it.
            $avgDesc = $qb->select('avg(length(m.description))')->getQuery()->getSingleScalarResult();
        } catch (\Exception $e) {
            $avgDesc = 0;
        }

        return [
            'totalMeals' => $totalMeals,
            'withImage' => $withImage,
            'withoutImage' => $withoutImage,
            'avgDescriptionLength' => $avgDesc,
        ];
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
