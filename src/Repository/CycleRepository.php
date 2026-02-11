<?php

namespace App\Repository;

use App\Entity\Cycle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Cycle>
 */
class CycleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cycle::class);
    }
// src/Repository/CycleRepository.php

public function getAverageCycleLength(): ?float
{
    $qb = $this->createQueryBuilder('c')
        ->select('AVG(DATE_DIFF(c.dateFinM, c.dateDebutM)) as avgCycle');

    return $qb->getQuery()->getSingleScalarResult();
}
// src/Repository/CycleRepository.php
// src/Repository/CycleRepository.php

public function findAllCyclesWithCycleLength()
{
    $cycles = $this->createQueryBuilder('c')
        ->orderBy('c.dateDebutM', 'ASC')
        ->getQuery()
        ->getResult();

    $result = [];
    $count = count($cycles);

    for ($i = 0; $i < $count; $i++) {
        $current = $cycles[$i];
        $next = $i < $count - 1 ? $cycles[$i + 1] : null;

        $cycleLength = null;
        if ($next) {
            $cycleLength = $current->getDateDebutM()->diff($next->getDateDebutM())->days;
        }

        $result[] = [
            'cycle' => $current,
            'cycleLength' => $cycleLength // null pour le dernier cycle
        ];
    }

    return $result;
}
//    /**
//     * @return Cycle[] Returns an array of Cycle objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Cycle
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
