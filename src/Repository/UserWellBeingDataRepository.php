<?php

namespace App\Repository;

use App\Entity\UserWellBeingData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserWellBeingData>
 */
class UserWellBeingDataRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserWellBeingData::class);
    }
    public function searchByTerm(?string $term): array
    {
        $qb = $this->createQueryBuilder('u');

        if ($term) {
            $qb->andWhere(
                'u.workEnvironment LIKE :term OR
                 u.sleepProblems LIKE :term OR
                 u.headaches LIKE :term OR
                 u.restlessness LIKE :term OR
                 u.lowAcademicConfidence LIKE :term'
            )
                ->setParameter('term', '%' . $term . '%');
        }

        return $qb->orderBy('u.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
    public function findBySearchAndSort(?string $term, ?array $sort = []): array
    {
        $qb = $this->createQueryBuilder('u');

        if ($term) {
            $qb->andWhere(
                'u.workEnvironment = :term OR 
             u.sleepProblems = :term OR 
             u.headaches = :term OR 
             u.restlessness = :term OR
             u.heartbeatPalpitations = :term OR
             u.lowAcademicConfidence = :term OR
             u.classAttendance = :term OR
             u.anxietyTension = :term OR
             u.irritability = :term OR
             u.subjectConfidence = :term'
            )
                ->setParameter('term', (int)$term);
        }

        if ($sort) {
            foreach ($sort as $field => $direction) {
                $direction = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';
                $qb->addOrderBy('u.' . $field, $direction);
            }
        } else {
            $qb->orderBy('u.createdAt', 'DESC');
        }

        return $qb->getQuery()->getResult();
    }
    public function getStatistics(?string $searchTerm = null): array
    {
        $qb = $this->createQueryBuilder('u');

        // Optional search filter
        if ($searchTerm) {
            $qb->andWhere('
            u.workEnvironment LIKE :term OR 
            u.sleepProblems LIKE :term OR 
            u.headaches LIKE :term
        ')
                ->setParameter('term', '%' . $searchTerm . '%');
        }

        $users = $qb->getQuery()->getResult();

        $totalUsers = count($users);
        $highAnxiety = count(array_filter($users, fn($u) => $u->getAnxietyTension() >= 4));
        $averageSleepProblems = $totalUsers ? array_sum(array_map(fn($u) => $u->getSleepProblems(), $users)) / $totalUsers : 0;
        $averageHeartbeat = $totalUsers ? array_sum(array_map(fn($u) => $u->getHeartbeatPalpitations(), $users)) / $totalUsers : 0;

        return [
            'totalUsers' => $totalUsers,
            'highAnxiety' => $highAnxiety,
            'averageSleepProblems' => round($averageSleepProblems, 2),
            'averageHeartbeat' => round($averageHeartbeat, 2),
        ];
    }


    //    /**
    //     * @return UserWellBeingData[] Returns an array of UserWellBeingData objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?UserWellBeingData
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
