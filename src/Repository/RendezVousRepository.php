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
     * Recherche paginée filtrée par patient connecté
     */
    public function searchPaginatedByPatient(
        int $patientId,
        string $search,
        int $page,
        int $itemsPerPage,
        string $sort = 'date'
    ): array {
        $qb = $this->createQueryBuilder('r')
            ->leftJoin('r.patient', 'p')
            ->leftJoin('r.medecin', 'm')
            ->where('r.patient = :patientId')
            ->setParameter('patientId', $patientId);

        // Recherche
        if (!empty($search)) {
            $qb->andWhere('
                p.nom LIKE :search OR 
                p.prenom LIKE :search OR 
                m.nom LIKE :search OR 
                m.prenom LIKE :search OR
                r.statut LIKE :search
            ')
            ->setParameter('search', '%' . $search . '%');
        }

        // Tri
        switch ($sort) {
            case 'patient':
                $qb->orderBy('p.nom', 'ASC');
                break;
            case 'statut':
                $qb->orderBy('r.statut', 'ASC');
                break;
            default: // date
                $qb->orderBy('r.dateRendezVous', 'DESC');
        }

        // Total
        $total = (clone $qb)
            ->select('COUNT(r.id)')
            ->getQuery()
            ->getSingleScalarResult();

        // Pagination
        $data = $qb
            ->setFirstResult(($page - 1) * $itemsPerPage)
            ->setMaxResults($itemsPerPage)
            ->getQuery()
            ->getResult();

        return [
            'data' => $data,
            'total' => $total
        ];
    }

    /**
     * Liste paginée filtrée par patient connecté (sans recherche)
     */
    public function findPaginatedByPatient(
        int $patientId,
        int $page,
        int $itemsPerPage,
        string $sort = 'date'
    ): array {
        return $this->searchPaginatedByPatient(
            $patientId,
            '',  // Pas de recherche
            $page,
            $itemsPerPage,
            $sort
        );
    }

    /**
     * Recherche paginée (TOUS les rendez-vous - pour admin)
     */
    public function searchPaginated(
        string $search,
        int $page,
        int $itemsPerPage,
        string $sort = 'date'
    ): array {
        $qb = $this->createQueryBuilder('r')
            ->leftJoin('r.patient', 'p')
            ->leftJoin('r.medecin', 'm');

        // Recherche
        if (!empty($search)) {
            $qb->where('
                p.nom LIKE :search OR 
                p.prenom LIKE :search OR 
                m.nom LIKE :search OR 
                m.prenom LIKE :search OR
                r.statut LIKE :search
            ')
            ->setParameter('search', '%' . $search . '%');
        }

        // Tri
        switch ($sort) {
            case 'patient':
                $qb->orderBy('p.nom', 'ASC');
                break;
            case 'statut':
                $qb->orderBy('r.statut', 'ASC');
                break;
            default: // date
                $qb->orderBy('r.dateRendezVous', 'DESC');
        }

        // Total
        $total = (clone $qb)
            ->select('COUNT(r.id)')
            ->getQuery()
            ->getSingleScalarResult();

        // Pagination
        $data = $qb
            ->setFirstResult(($page - 1) * $itemsPerPage)
            ->setMaxResults($itemsPerPage)
            ->getQuery()
            ->getResult();

        return [
            'data' => $data,
            'total' => $total
        ];
    }

    /**
     * Liste paginée (TOUS les rendez-vous - pour admin)
     */
    public function findPaginated(
        int $page,
        int $itemsPerPage,
        string $sort = 'date'
    ): array {
        return $this->searchPaginated(
            '',  // Pas de recherche
            $page,
            $itemsPerPage,
            $sort
        );
    }
}