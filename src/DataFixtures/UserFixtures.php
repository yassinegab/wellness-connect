<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Patient test
        $patient = new User();
        $patient->setEmail('patient@test.com');
        $patient->setNom('Dupont');
        $patient->setPrenom('Jean');
        $patient->setTelephone('+216 12 345 678');
        $patient->setRoles(['ROLE_PATIENT']);
        $patient->setPassword($this->passwordHasher->hashPassword($patient, 'patient123'));
        $manager->persist($patient);

        // Médecin test
        $medecin = new User();
        $medecin->setEmail('medecin@test.com');
        $medecin->setNom('Martin');
        $medecin->setPrenom('Dr. Marie');
        $medecin->setTelephone('+216 98 765 432');
        $medecin->setRoles(['ROLE_MEDECIN']);
        $medecin->setPassword($this->passwordHasher->hashPassword($medecin, 'medecin123'));
        $manager->persist($medecin);

        $manager->flush();
    }
}