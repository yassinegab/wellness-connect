<?php

namespace App\Repository;

use App\Entity\Front_office\Hopital;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class HopitalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Hopital::class);
    }

    /**
     * Recherche simple
     */
    public function search(string $term): array
    {
        return $this->createQueryBuilder('h')
            ->where('h.nom LIKE :term')
            ->orWhere('h.adresse LIKE :term')
            ->orWhere('h.tel LIKE :term')
            ->setParameter('term', '%' . $term . '%')
            ->orderBy('h.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche avec pagination et tri
     */
    public function searchPaginated(string $term, int $page = 1, int $limit = 10, string $sort = 'nom'): array
    {
        $qb = $this->createQueryBuilder('h')
            ->where('h.nom LIKE :term')
            ->orWhere('h.adresse LIKE :term')
            ->orWhere('h.tel LIKE :term')
            ->setParameter('term', '%' . $term . '%');

        // Tri
        switch ($sort) {
            case 'capacite':
                $qb->orderBy('h.capacite', 'DESC');
                break;
            case 'urgence':
                $qb->orderBy('h.serviceUrgenceDispo', 'DESC')
                   ->addOrderBy('h.nom', 'ASC');
                break;
            default:
                $qb->orderBy('h.nom', 'ASC');
        }

        // Compter le total
        $total = count($qb->getQuery()->getResult());

        // Pagination
        $offset = ($page - 1) * $limit;
        $data = $qb->setFirstResult($offset)
                   ->setMaxResults($limit)
                   ->getQuery()
                   ->getResult();

        return [
            'data' => $data,
            'total' => $total,
        ];
    }

    /**
     * Listing avec pagination et tri
     */
    public function findPaginated(int $page = 1, int $limit = 10, string $sort = 'nom'): array
    {
        $qb = $this->createQueryBuilder('h');

        // Tri
        switch ($sort) {
            case 'capacite':
                $qb->orderBy('h.capacite', 'DESC');
                break;
            case 'urgence':
                $qb->orderBy('h.serviceUrgenceDispo', 'DESC')
                   ->addOrderBy('h.nom', 'ASC');
                break;
            default:
                $qb->orderBy('h.nom', 'ASC');
        }

        // Compter le total
        $total = count($qb->getQuery()->getResult());

        // Pagination
        $offset = ($page - 1) * $limit;
        $data = $qb->setFirstResult($offset)
                   ->setMaxResults($limit)
                   ->getQuery()
                   ->getResult();

        return [
            'data' => $data,
            'total' => $total,
        ];
    }
}