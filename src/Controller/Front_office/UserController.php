<?php

namespace App\Controller\Front_office;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    // Dashboard Admin
    // #[Route('/dashboard/admin', name: 'admin_dashboard')]
    // public function admin(): Response
    // {
    //     // Vérifie que l'utilisateur est bien admin
    //     $this->denyAccessUnlessGranted('ROLE_ADMIN');

    //     // Affiche le fichier Twig correspondant
    //     return $this->render('dashboard/admin.html.twig');
    // }

    // Dashboard Médecin
#[Route('/dashboard/medecin', name: 'medecin_dashboard')]
public function medecin(): Response
{
    return $this->render('Front_office/medecin/index.html.twig', [
        'user' => $this->getUser(),
    ]);
}

    // Dashboard Patient
    #[Route('/dashboard/patient', name: 'patient_dashboard')]
    public function patient(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_PATIENT');

        return $this->render('dashboard/patient.html.twig');
    }
}
