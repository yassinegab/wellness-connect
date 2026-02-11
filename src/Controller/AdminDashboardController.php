<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminDashboardController extends AbstractController
{
    #[Route('/admin/dashboard', name: 'admin_dashboard')]
    public function index(UserRepository $userRepository): Response
    {
        $utilisatrices = $userRepository->findFemmes();
        $totalUsers = $userRepository->count([]);
$totalFemmes = $userRepository->count(['sexe' => 'female']);

$femmesAvecCycle = $totalFemmes;

$pourcentageSuivi = $totalFemmes > 0 ? 100 : 0;

return $this->render('admin/dashboard/index.html.twig', [
    'utilisatrices' => $utilisatrices,
    'totalUsers' => $totalUsers,
    'totalFemmes' => $totalFemmes,
    'femmesAvecCycle' => $femmesAvecCycle,
    'pourcentageSuivi' => $pourcentageSuivi,
]);

    }}     