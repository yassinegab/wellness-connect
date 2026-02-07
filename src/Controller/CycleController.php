<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CycleController extends AbstractController
{
    #[Route('/cycle', name: 'cycle')]
    public function index(): Response
{
     // Example user data
        $user = [
            'prenom' => 'souha',

        ];

        // Example quick actions
        $quickActions = [
            ['emoji' => '💊', 'label' => 'Médicaments'],
            ['emoji' => '🧘', 'label' => 'Méditation'],
            ['emoji' => '🏃', 'label' => 'Exercice'],
            ['emoji' => '🥗', 'label' => 'Nutrition'],
        ];
    
        return $this->render('cycle/cycle.html.twig', [
            'controller_name' => 'CycleController',
            'user' => $user,
            'quickActions' => $quickActions,
        ]);
    }
}


    
       

