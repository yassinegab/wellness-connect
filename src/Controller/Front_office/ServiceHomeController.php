<?php

namespace App\Controller\Front_office;

use App\Repository\UserRepository;
use App\Repository\HopitalRepository;
use App\Repository\RendezVousRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ServiceHomeController extends AbstractController
{
    #[Route('/services', name: 'app_service_home')]
    public function index(
        Request $request,
        UserRepository $userRepository,
        HopitalRepository $hopitalRepository,
        RendezVousRepository $rendezVousRepository
    ): Response {

        // ✅ récupération recherche + filtre
        $search = $request->query->get('q', '');
        $filter = $request->query->get('filter', 'all');

        $hopitaux = [];
        $rendezvous = [];

        // ✅ logique recherche
        if ($search) {

            if ($filter === 'hopital') {
                $hopitaux = $hopitalRepository->search($search);

            } elseif ($filter === 'rdv') {
                $rendezvous = $rendezVousRepository->search($search);

            } else {
                $hopitaux = $hopitalRepository->search($search);
                $rendezvous = $rendezVousRepository->search($search);
            }

        } else {
            $hopitaux = $hopitalRepository->findAll();
            $rendezvous = $rendezVousRepository->findBy([], ['id' => 'DESC'], 5);
        }

        // ✅ statistiques globales
        $medecinCount = $userRepository->countMedecins();
        $patientCount = $userRepository->countPatients();
        $hopitalCount = count($hopitaux);

        return $this->render('Front_office/service_home/index.html.twig', [
            'hopitaux'      => $hopitaux,
            'rendezvous'    => $rendezvous,
            'medecin_count' => $medecinCount,
            'patient_count' => $patientCount,
            'hopital_count' => $hopitalCount,
            'search'        => $search,
            'filter'        => $filter,
        ]);
    }
}
