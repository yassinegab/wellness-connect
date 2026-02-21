<?php

namespace App\Controller;

use App\Repository\HopitalRepository;
use App\Repository\RendezVousRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\StressPrediction;

class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard')]
    public function index(
        EntityManagerInterface $em,
        RendezVousRepository $rendezVousRepository,
        HopitalRepository $hopitalRepository
    ): Response
    {
        $user = $this->getUser();
        
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Medical Services Statistics
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

        // Données utilisateur pour l'affichage (si nécessaire, sinon utiliser app.user dans twig)
        $userData = [
            'prenom' => $user->getPrenom(),
            'nom' => $user->getNom(),
        ];

        // Actions rapides
        $quickActions = [
            ['emoji' => '💊', 'label' => 'Médicaments'],
            ['emoji' => '🧘', 'label' => 'Méditation'],
            ['emoji' => '🏃', 'label' => 'Exercice'],
            ['emoji' => '🥗', 'label' => 'Nutrition'],
        ];

        // Stress Statistics for Admin (or User History)
        $allPredictions = $em->getRepository(StressPrediction::class)->findBy([], ['createdAt' => 'ASC']);
        
        $stats = [
            'Low' => 0,
            'Moderate' => 0,
            'High' => 0
        ];
        
        $scatterData = [];
        foreach ($allPredictions as $p) {
            $label = $p->getPredictedStressType();
            if (isset($stats[$label])) {
                $stats[$label]++;
            }
            
            $scatterData[] = [
                'x' => $p->getCreatedAt()->format('Y-m-d H:i'),
                'y' => $p->getConfidenceScore(),
                'user' => $p->getUserWellBeingData()->getUser() ? $p->getUserWellBeingData()->getUser()->getNom() : 'Anon',
                'category' => $label
            ];
        }

        return $this->render('dashboard/index.html.twig', [
            'user' => $userData,
            'quickActions' => $quickActions,
            'stressStats' => $stats,
            'scatterData' => $scatterData,
            'upcomingAppointments' => $upcomingAppointments,
            'completedConsultations' => $completedConsultations,
            'availableHospitals' => $availableHospitals,
        ]);
    }
}
