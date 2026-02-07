<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Cycle;


final class CycleController extends AbstractController
{
   
    #[Route('/cycle', name: 'cycle')]
    public function index(EntityManagerInterface $em): Response
    {
        $user = [
            'prenom' => 'Souha',
        ];

        // Récupérer les cycles existants
        $cycles = $em->getRepository(Cycle::class)->findAll();

        // 🔥 Préparer les events pour FullCalendar
        $calendarEvents = [];

        foreach ($cycles as $cycle) {
            $calendarEvents[] = [
                'title' => 'Cycle menstruel',
                'start' => $cycle->getDateDebutM()->format('Y-m-d'),
                'end'   => $cycle->getDateFinM()->modify('+1 day')->format('Y-m-d'),
                'color' => '#ef4444',
            ];
        }

       // return $this->render('cycle/cycle.html.twig', [
         //   'user' => $user,
       //     'calendarEvents' => json_encode($calendarEvents), // ⭐ IMPORTANT
      //  ]);
    

  

        // Example quick actions
        $quickActions = [
            ['emoji' => '💊', 'label' => 'Médicaments'],
            ['emoji' => '🧘', 'label' => 'Méditation'],
            ['emoji' => '🏃', 'label' => 'Exercice'],
            ['emoji' => '🥗', 'label' => 'Nutrition'],
        ];
    
          // Récupérer les cycles existants
    $cycles = $em->getRepository(Cycle::class)->findAll();

    return $this->render('cycle/cycle.html.twig', [
        'controller_name' => 'CycleController',
        'user' => $user,
        'cycles' => $cycles,
        'quickActions' => $quickActions,
         'calendarEvents' => json_encode($calendarEvents), // ⭐ IMPORTANT
    ]);

    }
     #[Route('/cycle/add', name: 'cycle_add_ajax', methods: ['POST'])]
    public function addCycleAjax(
        Request $request,
        EntityManagerInterface $em
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $start = new \DateTime($data['start']);
        $end = new \DateTime($data['end']);
        $end->modify('-1 day'); // FullCalendar end exclusive

        $cycle = new Cycle();
        $cycle->setDateDebutM($start);
        $cycle->setDateFinM($end);

        $em->persist($cycle);
        $em->flush();

        return new JsonResponse(['success' => true]);
    }
}


    
       

