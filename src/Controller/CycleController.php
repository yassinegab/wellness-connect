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

    $calendarEvents = [];

/*foreach ($cycles as $cycle) {
    $calendarEvents[] = [
        'title' => '',
        'start' => $cycle->getDateDebutM()->format('Y-m-d'),
        'end'   => $cycle->getDateFinM()->format('Y-m-d'),
        'allDay' => true
    ];
}*/

foreach ($cycles as $cycle) {
    $start = $cycle->getDateDebutM();
    $end = $cycle->getDateFinM();

    $current = clone $start;
    while ($current <= $end) {
        $calendarEvents[] = [
            'title' => '🩸',                    // emoji goutte de sang
            'start' => $current->format('Y-m-d'),
            'allDay' => true,
            'classNames' => ['menstruation-event']
        ];
        $current->modify('+1 day');
    }
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

    if (!isset($data['start'], $data['end'])) {
        return new JsonResponse(['success' => false, 'message' => 'Dates manquantes'], 400);
    }

    $start = new \DateTime($data['start']);
    $endExclusive = new \DateTime($data['end']);

    // FullCalendar => end exclusif
    $end = clone $endExclusive;
    $end->modify('-1 day');

    // 🔐 Vérification AVANT persist
    if ($start > $end) {
        return new JsonResponse([
            'success' => false,
            'message' => 'La date de début doit être avant la date de fin'
        ], 400);
    }

    $cycle = new Cycle();
    $cycle->setDateDebutM($start);
    $cycle->setDateFinM($end);

    $em->persist($cycle);
    $em->flush();

    return new JsonResponse([
        'success' => true
    ]);
}
    
}


    
       

