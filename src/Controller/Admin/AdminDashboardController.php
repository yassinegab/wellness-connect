<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class AdminDashboardController extends AbstractController
{
    /**
     * Redirect /admin to /admin/dashboard
     */
    #[Route('', name: 'admin_index')]
    public function adminIndex(): Response
    {
        return $this->redirectToRoute('admin_dashboard');
    }

    #[Route('/dashboard', name: 'admin_dashboard')]
    public function index(): Response
    {
        // ---- PLACEHOLDER DATA ----
        $totalUsers = 10;          // fake number of users
        $totalAppointments = 5;    // fake number of appointments
        $totalHospitals = 3;       // fake number of hospitals

        // Dummy admin info
        $admin = [
            'prenom' => 'Admin',
            'nom' => 'Placeholder',
        ];

        // Quick action buttons (unchanged)
        $quickActions = [
            ['emoji' => '👥', 'label' => 'Utilisateurs'],
            ['emoji' => '📅', 'label' => 'Rendez-vous'],
            ['emoji' => '🏥', 'label' => 'Hôpitaux'],
            ['emoji' => '📊', 'label' => 'Statistiques'],
        ];

        return $this->render('admin/dashboard/index.html.twig', [
            'admin' => $admin,
            'quickActions' => $quickActions,
            'totalUsers' => $totalUsers,
            'totalAppointments' => $totalAppointments,
            'totalHospitals' => $totalHospitals,
        ]);
    }
}
