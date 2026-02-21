<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'frontoffice_dashboard')]
    public function index(
        \Doctrine\ORM\EntityManagerInterface $em
    ): Response
    {
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
            'stressStats' => $stats,
            'scatterData' => $scatterData,
        ]);
    }
}
