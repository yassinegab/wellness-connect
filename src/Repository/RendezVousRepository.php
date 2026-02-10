<?php

namespace App\Repository;

use App\Entity\Front_office\RendezVous;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class RendezVousRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RendezVous::class);
    }

    /**
     * Recherche simple
     */
    public function search(string $term): array
    {
        return $this->createQueryBuilder('r')
            ->leftJoin('r.patient', 'p')
            ->leftJoin('r.medecin', 'm')
            ->leftJoin('r.hopital', 'h')
            ->where('p.nom LIKE :term')
            ->orWhere('p.prenom LIKE :term')
            ->orWhere('m.nom LIKE :term')
            ->orWhere('m.prenom LIKE :term')
            ->orWhere('h.nom LIKE :term')
            ->orWhere('r.statut LIKE :term')
            ->orWhere('r.typeConsultation LIKE :term')
            ->setParameter('term', '%' . $term . '%')
            ->orderBy('r.dateRendezVous', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche avec pagination et tri
     */
    public function searchPaginated(string $term, int $page = 1, int $limit = 10, string $sort = 'date'): array
    {
        $qb = $this->createQueryBuilder('r')
            ->leftJoin('r.patient', 'p')
            ->leftJoin('r.medecin', 'm')
            ->leftJoin('r.hopital', 'h')
            ->where('p.nom LIKE :term')
            ->orWhere('p.prenom LIKE :term')
            ->orWhere('m.nom LIKE :term')
            ->orWhere('m.prenom LIKE :term')
            ->orWhere('h.nom LIKE :term')
            ->orWhere('r.statut LIKE :term')
            ->setParameter('term', '%' . $term . '%');

        // Tri
        switch ($sort) {
            case 'patient':
                $qb->orderBy('p.nom', 'ASC');
                break;
            case 'statut':
                $qb->orderBy('r.statut', 'ASC')
                   ->addOrderBy('r.dateRendezVous', 'DESC');
                break;
            default:
                $qb->orderBy('r.dateRendezVous', 'DESC');
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
    public function findPaginated(int $page = 1, int $limit = 10, string $sort = 'date'): array
    {
        $qb = $this->createQueryBuilder('r')
            ->leftJoin('r.patient', 'p')
            ->leftJoin('r.medecin', 'm')
            ->leftJoin('r.hopital', 'h');

        // Tri
        switch ($sort) {
            case 'patient':
                $qb->orderBy('p.nom', 'ASC');
                break;
            case 'statut':
                $qb->orderBy('r.statut', 'ASC')
                   ->addOrderBy('r.dateRendezVous', 'DESC');
                break;
            default:
                $qb->orderBy('r.dateRendezVous', 'DESC');
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