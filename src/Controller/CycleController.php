<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CycleRepository;

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
            'id' => $cycle->getIdCycle(),
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
    EntityManagerInterface $em,
    ValidatorInterface $validator
): JsonResponse {

    $data = json_decode($request->getContent(), true);

    if (!isset($data['start'], $data['end'])) {
        return new JsonResponse([
            'success' => false,
            'message' => 'Dates manquantes'
        ], 400);
    }

    $start = new \DateTime($data['start']);
    $endExclusive = new \DateTime($data['end']);

    // FullCalendar → end exclusif
    $end = clone $endExclusive;
    $end->modify('-1 day');

    /* ===========================
       1️⃣ Validation logique dates
       =========================== */
    if ($start >= $end) {
        return new JsonResponse([
            'success' => false,
            'message' =>  'La période doit durer au moins 2 jours 🌸'
        ], 400);
    }

    /* ===========================
       2️⃣ Vérification chevauchement
       =========================== */
    $existingCycles = $em->getRepository(Cycle::class)->findAll();

    foreach ($existingCycles as $cycle) {
        $existingStart = $cycle->getDateDebutM();
        $existingEnd   = $cycle->getDateFinM();

        if ($start <= $existingEnd && $end >= $existingStart) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Oops 😅 cette période est déjà notée!'
            ], 409);
        }
    }

    /* ===========================
       3️⃣ Sauvegarde autorisée
       =========================== */
    $cycle = new Cycle();
$cycle->setDateDebutM($start);
$cycle->setDateFinM($end);

// 🔐 Validation Symfony
$errors = $validator->validate($cycle);

if (count($errors) > 0) {
    return new JsonResponse([
        'success' => false,
        'message' => $errors[0]->getMessage()
    ], 400);
}

    $em->persist($cycle);
    $em->flush();

    return new JsonResponse([
        'success' => true
    ]);
}
#[Route('/cycle/delete', name: 'cycle_delete_ajax', methods: ['POST'])]
public function deleteCycleAjax(Request $request, EntityManagerInterface $em): JsonResponse
{
    $data = json_decode($request->getContent(), true);

    if (!isset($data['id'])) {
        return new JsonResponse(['success' => false, 'message' => 'ID manquant'], 400);
    }

    $cycle = $em->getRepository(Cycle::class)->find($data['id']);

    if (!$cycle) {
        return new JsonResponse(['success' => false, 'message' => 'Cycle introuvable'], 404);
    }

    $em->remove($cycle);
    $em->flush();

    return new JsonResponse(['success' => true]);
}

#[Route('/cycle/update', name: 'cycle_update_ajax', methods: ['POST'])]
public function updateCycleAjax(Request $request, EntityManagerInterface $em): JsonResponse
{
    $data = json_decode($request->getContent(), true);

    if (!isset($data['id'], $data['start'], $data['end'])) {
        return new JsonResponse(['success' => false, 'message' => 'Données manquantes'], 400);
    }

    $cycle = $em->getRepository(Cycle::class)->find($data['id']);
    if (!$cycle) {
        return new JsonResponse(['success' => false, 'message' => 'Cycle introuvable'], 404);
    }

    $cycle->setDateDebutM(new \DateTime($data['start']));
    $cycle->setDateFinM(new \DateTime($data['end']));
    $em->flush();

    return new JsonResponse(['success' => true]);
}

#[Route('/cycle/search', name: 'cycle_search')]
public function searchByYear(Request $request, CycleRepository $cycleRepository): Response
{
    $year = $request->query->get('year');

    if (!$year) {
        return $this->json(['error' => 'Year parameter missing'], 400);
    }

    // Récupère les cycles dont la dateDebut est dans l'année demandée
    $cycles = $cycleRepository->createQueryBuilder('c')
        ->where('YEAR(c.dateDebut) = :year')
        ->setParameter('year', $year)
        ->orderBy('c.dateDebut', 'ASC')
        ->getQuery()
        ->getResult();

    // Préparer un tableau simple pour le JSON
    $data = [];
    foreach ($cycles as $cycle) {
        $data[] = [
            'id' => $cycle->getIdCycle(),
            'dateDebut' => $cycle->getDateDebutM()->format('Y-m-d'),
            'dateFin' => $cycle->getDateFinM()->format('Y-m-d'),
            'duration' => $cycle->getDateDebutM()->diff($cycle->getDateFinM())->days
        ];
    }

    return $this->json($data);
}
// src/Controller/CycleController.php
#[Route('/cycle/history', name: 'calendar_cycles_history')]
public function history(CycleRepository $cycleRepository): Response
{
    // Récupère tous les cycles triés par date
    $cycles = $cycleRepository->findBy([], ['dateDebutM' => 'ASC']);

    return $this->render('cycle/history.html.twig', [
        'cycles' => $cycles, // ✅ variable envoyée au template
    ]);

}
    
}


    
       