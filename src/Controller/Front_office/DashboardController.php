<?php

namespace App\Controller\Front_office;

use App\Repository\RendezVousRepository;
use App\Repository\HopitalRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/', name: 'frontoffice_dashboard')]
    public function index(
        RendezVousRepository $rendezVousRepository,
        HopitalRepository $hopitalRepository
    ): Response
    {
        $user = $this->getUser();
        
        // ✅ CORRECTION: Rediriger vers la route de login au lieu de rendre le template
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        
        // Statistiques pour l'utilisateur connecté
        $upcomingAppointments = $rendezVousRepository->count([
            'patient' => $user,
            'statut' => 'En attente'
        ]);
        
        $completedConsultations = $rendezVousRepository->count([
            'patient' => $user,
            'statut' => 'Terminé'
        ]);
        
        $availableHospitals = $hopitalRepository->count([
            'serviceUrgenceDispo' => true
        ]);

        $quickActions = [
            ['emoji' => '💊', 'label' => 'Médicaments'],
            ['emoji' => '🧘', 'label' => 'Méditation'],
            ['emoji' => '🏃', 'label' => 'Exercice'],
            ['emoji' => '🥗', 'label' => 'Nutrition'],
        ];

        return $this->render('dashboard/index.html.twig', [
            'user' => $user,
            'quickActions' => $quickActions,
            'upcomingAppointments' => $upcomingAppointments,
            'completedConsultations' => $completedConsultations,
            'availableHospitals' => $availableHospitals,
        ]);
    }
}