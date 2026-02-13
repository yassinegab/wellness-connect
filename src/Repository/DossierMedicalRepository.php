<?php

namespace App\Repository;

use App\Entity\DossierMedical;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DossierMedical>
 */
class DossierMedicalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DossierMedical::class);
    }

    /**
     * Récupérer le dossier médical d’un utilisateur (patient)
     */
    public function findByUser(User $user): ?DossierMedical
    {
        return $this->findOneBy([
            'user' => $user
        ]);
    }

    /**
     * Vérifier si un utilisateur possède déjà un dossier médical
     */
    public function existsForUser(User $user): bool
    {
        return (bool) $this->createQueryBuilder('d')
            ->select('COUNT(d.id)')
            ->where('d.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Récupérer tous les dossiers médicaux (ADMIN / MEDECIN)
     */
    public function findAllDossiers(): array
    {
        return $this->createQueryBuilder('d')
            ->leftJoin('d.user', 'u')
            ->addSelect('u')
            ->orderBy('u.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
