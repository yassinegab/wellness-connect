<?php

namespace App\Controller\Front_office;

use App\Repository\RendezVousRepository;
use App\Repository\HopitalRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DashboardController extends AbstractController
{
    #[Route('/', name: 'frontoffice_dashboard')]
    #[IsGranted('ROLE_USER')] // ✅ Redirige automatiquement vers login si non connecté
    public function index(
        RendezVousRepository $rendezVousRepository,
        HopitalRepository $hopitalRepository
    ): Response
    {
        // ========================================
        // 1. RÉCUPÉRATION DE L'UTILISATEUR CONNECTÉ
        // ========================================
        $user = $this->getUser();
        
        // ✅ Sécurité supplémentaire : vérifier si l'utilisateur existe
        if (!$user) {
            // Ajouter un message flash
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page.');
            // Rediriger vers la page de login
            return $this->redirectToRoute('app_login');
        }

        // ========================================
        // 2. STATISTIQUES DU DASHBOARD
        // ========================================
        
        // Rendez-vous à venir pour l'utilisateur connecté
        $upcomingAppointments = $rendezVousRepository->count([
            'patient' => $user,
            'statut' => 'En attente'
        ]);
        
        // Consultations terminées pour l'utilisateur connecté
        $completedConsultations = $rendezVousRepository->count([
            'patient' => $user,
            'statut' => 'Terminé'
        ]);
        
        // Hôpitaux avec service d'urgence disponible
        $availableHospitals = $hopitalRepository->count([
            'serviceUrgenceDispo' => true
        ]);

        // ========================================
        // 3. ACTIONS RAPIDES
        // ========================================
        $quickActions = [
            ['emoji' => '💊', 'label' => 'Médicaments'],
            ['emoji' => '🧘', 'label' => 'Méditation'],
            ['emoji' => '🏃', 'label' => 'Exercice'],
            ['emoji' => '🥗', 'label' => 'Nutrition'],
        ];

        // ========================================
        // 4. RENDU DE LA VUE
        // ========================================
        return $this->render('dashboard/index.html.twig', [
            'user' => $user, // ✅ Passer l'utilisateur complet au template
            'quickActions' => $quickActions,
            'upcomingAppointments' => $upcomingAppointments,
            'completedConsultations' => $completedConsultations,
            'availableHospitals' => $availableHospitals,
        ]);
    }
}