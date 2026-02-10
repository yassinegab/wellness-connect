<?php

namespace App\Repository;

use App\Entity\Front_office\User;
use App\Enum\UserRole; // <-- IMPORTANT: cet import doit exister
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class); // <-- User::class doit pointer vers la bonne entité
    }

    // ... autres méthodes

    public function findMedecins(): array
    {
        return $this->createQueryBuilder('u')
            ->where('u.role = :role') // <-- "role" au singulier, pas "roles"
            ->setParameter('role', UserRole::MEDECIN) // <-- Utilisation de l'Enum
            ->getQuery()
            ->getResult();
    }

    public function findPatients(): array
    {
        return $this->createQueryBuilder('u')
            ->where('u.role = :role')
            ->setParameter('role', UserRole::PATIENT)
            ->getQuery()
            ->getResult();
    }

    public function countMedecins(): int
    {
        return $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->where('u.role = :role')
            ->setParameter('role', UserRole::MEDECIN)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countPatients(): int
    {
        return $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->where('u.role = :role')
            ->setParameter('role', UserRole::PATIENT)
            ->getQuery()
            ->getSingleScalarResult();
    }
}