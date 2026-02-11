<?php
namespace App\Controller;

use App\Repository\CycleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('cycle/stats')]
class StatsController extends AbstractController
{
    #[Route('/', name: 'admin_stats_index')]
    public function index(CycleRepository $cycleRepository): Response
    {
        $averageCycle = $cycleRepository->getAverageCycleLength();

        return $this->render('cycle/stats.html.twig', [
            'averageCycle' => $averageCycle,
        ]);
    }
}