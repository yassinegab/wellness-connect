<?php

namespace App\Controller\Admin;

use App\Service\StressPredictionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/ai')]
#[IsGranted('ROLE_ADMIN')]
class AdminAIController extends AbstractController
{
    #[Route('', name: 'admin_ai_dashboard')]
    public function index(StressPredictionService $predictionService): Response
    {
        $analysis = $predictionService->getAggregateRiskAnalysis();

        return $this->render('admin/ai_dashboard.html.twig', [
            'analysis' => $analysis,
        ]);
    }
}
