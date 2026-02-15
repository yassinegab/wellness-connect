<?php

namespace App\Controller;

use App\Repository\RendezVousRepository;
use App\Repository\HopitalRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'frontoffice_dashboard')]
    public function index(
        RendezVousRepository $rendezVousRepository,
        HopitalRepository $hopitalRepository,
        \Doctrine\ORM\EntityManagerInterface $em
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

        // Données utilisateur (exemple statique, remplacer par authentification réelle)
        $user = [
            'prenom' => $this->getUser() ? $this->getUser()->getPrenom() : 'Yassine',
            'nom' => $this->getUser() ? $this->getUser()->getNom() : '',
        ];

        // Actions rapides
        $quickActions = [
            ['emoji' => '💊', 'label' => 'Médicaments'],
            ['emoji' => '🧘', 'label' => 'Méditation'],
            ['emoji' => '🏃', 'label' => 'Exercice'],
            ['emoji' => '🥗', 'label' => 'Nutrition'],
        ];

        // Stress Statistics for Admin
        $allPredictions = $em->getRepository(\App\Entity\StressPrediction::class)->findBy([], ['createdAt' => 'ASC']);
        
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
            'user' => $user,
            'quickActions' => $quickActions,
            'upcomingAppointments' => $upcomingAppointments,
            'completedConsultations' => $completedConsultations,
            'availableHospitals' => $availableHospitals,
            'stressStats' => $stats,
            'scatterData' => $scatterData,
        ]);
    }
}
