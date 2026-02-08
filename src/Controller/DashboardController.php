<?php

// src/Controller/DashboardController.php
namespace App\Controller;

use App\Repository\RendezVousRepository;
use App\Repository\HopitalRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard')]
    public function index(
        RendezVousRepository $rendezVousRepository,
        HopitalRepository $hopitalRepository
    ): Response
    {
        // Statistiques du dashboard
        $upcomingAppointments = $rendezVousRepository->count([
            'statut' => 'En attente'
        ]);
        
        $completedConsultations = $rendezVousRepository->count([
            'statut' => 'Terminé'
        ]);
        
        $availableHospitals = $hopitalRepository->count([
            'serviceUrgenceDispo' => true
        ]);

        // Example user data
        $user = [
            'prenom' => 'Yassine',
        ];

        // Example quick actions
        $quickActions = [
            ['emoji' => '💊', 'label' => 'Médicaments'],
            ['emoji' => '🧘', 'label' => 'Méditation'],
            ['emoji' => '🏃', 'label' => 'Exercice'],
            ['emoji' => '🥗', 'label' => 'Nutrition'],
        ];

        return $this->render('dashboard/index.html.twig', [
            'upcomingAppointments' => $upcomingAppointments,
            'completedConsultations' => $completedConsultations,
            'availableHospitals' => $availableHospitals,
            'user' => $user,
            'quickActions' => $quickActions,
        ]);
    }
}
