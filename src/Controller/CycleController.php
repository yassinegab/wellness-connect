<?php

namespace App\Controller;

use App\Entity\Cycle;
use App\Form\CycleType;
use App\Repository\CycleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CycleController extends AbstractController
{
#[Route('/cycle', name: 'cycle_index')]
public function index(EntityManagerInterface $em): Response
{
    $cycles = $em->getRepository(Cycle::class)->findAll();
    $events = [];

    foreach ($cycles as $cycle) {

        $start = clone $cycle->getDateDebutM();
        $end   = clone $cycle->getDateFinM();

        while ($start <= $end) {

            $events[] = [
                'id' => $cycle->getIdCycle(),
                'title' => '🩸',
                'start' => $start->format('Y-m-d'),
                'allDay' => true,
                'classNames' => ['menstruation-event']
            ];

            $start->modify('+1 day');
        }
    }

    return $this->render('cycle/calendar.html.twig', [
        'calendarEvents' => json_encode($events),
    ]);
}

    #[Route('/cycle/new', name: 'cycle_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $cycle = new Cycle();
        $form = $this->createForm(CycleType::class, $cycle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($cycle);
            $em->flush();

            $this->addFlash('success', 'Cycle ajouté avec succès.');

            return $this->redirectToRoute('cycle_index');
        }

        return $this->render('cycle/form.html.twig', [
            'form' => $form->createView(),
            'isEdit' => false,
        ]);
    }

    #[Route('/cycle/{id}/edit', name: 'cycle_edit')]
    public function edit(Cycle $cycle, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(CycleType::class, $cycle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Cycle mis à jour.');

            return $this->redirectToRoute('cycle_index');
        }

        return $this->render('cycle/form.html.twig', [
            'form' => $form->createView(),
            'isEdit' => true,
        ]);
    }

    #[Route('/cycle/{id}/delete', name: 'cycle_delete', methods: ['GET', 'POST'])]
    public function delete(Request $request, Cycle $cycle, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('delete-cycle'.$cycle->getIdCycle(), $request->request->get('_token'))) {
                throw $this->createAccessDeniedException('Token invalide.');
            }

            $em->remove($cycle);
            $em->flush();

            $this->addFlash('success', 'Cycle supprimé.');

            return $this->redirectToRoute('cycle_index');
        }

        return $this->render('cycle/delete.html.twig', [
            'cycle' => $cycle,
        ]);
    }

    #[Route('/cycle/search', name: 'cycle_search')]
    public function searchByYear(Request $request, CycleRepository $cycleRepository): Response
    {
        $year = $request->query->get('year');

        if (!$year) {
            return $this->json(['error' => 'Year parameter missing'], 400);
        }

        $cycles = $cycleRepository->createQueryBuilder('c')
            ->where('YEAR(c.dateDebut) = :year')
            ->setParameter('year', $year)
            ->orderBy('c.dateDebut', 'ASC')
            ->getQuery()
            ->getResult();

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

    #[Route('/cycle/history', name: 'calendar_cycles_history')]
    public function history(CycleRepository $cycleRepository): Response
    {
        $cycles = $cycleRepository->findBy([], ['dateDebutM' => 'ASC']);

        return $this->render('cycle/history.html.twig', [
            'cycles' => $cycles,
        ]);
    }

    #[Route('/cycle/stats', name: 'cycle_stats')]
    public function stats(CycleRepository $cycleRepository): Response
    {
        $cycles = $cycleRepository->findBy([], ['dateDebutM' => 'ASC']);

        $cycleDurations = [];
        $totalDays = 0;

        foreach ($cycles as $cycle) {
            $days = $cycle->getDateDebutM()->diff($cycle->getDateFinM())->days + 1;
            $cycleDurations[] = [
                'start' => $cycle->getDateDebutM()->format('Y-m-d'),
                'end' => $cycle->getDateFinM()->format('Y-m-d'),
                'duration' => $days
            ];
            $totalDays += $days;
        }

        $averageCycle = count($cycles) > 0 ? round($totalDays / count($cycles), 1) : 0;

        $labels = [];
        $data = [];
        foreach ($cycleDurations as $c) {
            $labels[] = $c['start'];
            $data[] = $c['duration'];
        }

        return $this->render('cycle/stats.html.twig', [
            'averageCycle' => $averageCycle,
            'chartLabels' => json_encode($labels),
            'chartData' => json_encode($data)
        ]);
    }
}
