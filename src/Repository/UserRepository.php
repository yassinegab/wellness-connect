<?php

namespace App\Repository;

use App\Entity\Front_office\User;
use App\Enum\UserRole;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }
    
    /**
     * Compte le nombre de médecins
     */
    public function countMedecins(): int
    {
        return $this->count(['userRole' => UserRole::MEDECIN]);
    }
    
    /**
     * Compte le nombre de patients
     */
    public function countPatients(): int
    {
        return $this->count(['userRole' => UserRole::PATIENT]);
    }
    
    /**
     * Compte le nombre d'administrateurs
     */
    public function countAdmins(): int
    {
        return $this->count(['userRole' => UserRole::ADMIN]);
    }
    
    /**
     * Trouve tous les médecins
     */
    public function findAllMedecins(): array
    {
        return $this->findBy(['userRole' => UserRole::MEDECIN]);
    }
    
    /**
     * Trouve tous les patients
     */
    public function findAllPatients(): array
    {
        return $this->findBy(['userRole' => UserRole::PATIENT]);
    }
}