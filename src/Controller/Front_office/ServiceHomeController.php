<?php

namespace App\Controller\Front_office;

use App\Repository\UserRepository;
use App\Repository\HopitalRepository;
use App\Repository\RendezVousRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ServiceHomeController extends AbstractController
{
    #[Route('/services', name: 'app_service_home')]
    public function index(
        Request $request,
        UserRepository $userRepository,
        HopitalRepository $hopitalRepository,
        RendezVousRepository $rendezVousRepository,
        ValidatorInterface $validator
    ): Response {
        // ========================================
        // 1. RÉCUPÉRATION DES PARAMÈTRES
        // ========================================
        $search = $request->query->get('q', '');
        $filter = $request->query->get('filter', 'all');
        $sortHopital = $request->query->get('sort_hopital', 'nom');
        $sortRdv = $request->query->get('sort_rdv', 'date');
        $pageHopital = $request->query->get('page_hopital', 1);
        $pageRdv = $request->query->get('page_rdv', 1);

        // ========================================
        // 2. VALIDATION CÔTÉ SERVEUR (STRICTE)
        // ========================================
        $constraints = new Assert\Collection([
            'q' => [
                new Assert\Type(['type' => 'string']),
                new Assert\Length([
                    'max' => 100,
                    'maxMessage' => 'La recherche ne peut pas dépasser {{ limit }} caractères.'
                ]),
                new Assert\Regex([
                    'pattern' => '/^[a-zA-Z0-9\s\-\_éèêëàâäôöùûüçîïÉÈÊËÀÂÄÔÖÙÛÜÇÎÏ]*$/',
                    'message' => 'La recherche contient des caractères non autorisés.'
                ])
            ],
            'filter' => [
                new Assert\NotBlank(['message' => 'Le filtre est requis.']),
                new Assert\Choice([
                    'choices' => ['all', 'hopital', 'rdv'],
                    'message' => 'Le filtre "{{ value }}" n\'est pas valide. Les valeurs autorisées sont : {{ choices }}.'
                ])
            ],
            'sort_hopital' => [
                new Assert\NotBlank(['message' => 'Le tri hôpital est requis.']),
                new Assert\Choice([
                    'choices' => ['nom', 'capacite', 'urgence'],
                    'message' => 'Le tri hôpital "{{ value }}" n\'est pas valide.'
                ])
            ],
            'sort_rdv' => [
                new Assert\NotBlank(['message' => 'Le tri rendez-vous est requis.']),
                new Assert\Choice([
                    'choices' => ['date', 'patient', 'statut'],
                    'message' => 'Le tri rendez-vous "{{ value }}" n\'est pas valide.'
                ])
            ],
            'page_hopital' => [
                new Assert\Type(['type' => 'numeric', 'message' => 'La page hôpital doit être un nombre.']),
                new Assert\Positive(['message' => 'La page hôpital doit être un nombre positif.']),
                new Assert\LessThanOrEqual([
                    'value' => 1000,
                    'message' => 'La page hôpital ne peut pas dépasser {{ compared_value }}.'
                ])
            ],
            'page_rdv' => [
                new Assert\Type(['type' => 'numeric', 'message' => 'La page rendez-vous doit être un nombre.']),
                new Assert\Positive(['message' => 'La page rendez-vous doit être un nombre positif.']),
                new Assert\LessThanOrEqual([
                    'value' => 1000,
                    'message' => 'La page rendez-vous ne peut pas dépasser {{ compared_value }}.'
                ])
            ]
        ]);

        $input = [
            'q' => $search,
            'filter' => $filter,
            'sort_hopital' => $sortHopital,
            'sort_rdv' => $sortRdv,
            'page_hopital' => $pageHopital,
            'page_rdv' => $pageRdv
        ];

        $violations = $validator->validate($input, $constraints);

        // Si des violations sont détectées, rediriger avec message d'erreur
        if (count($violations) > 0) {
            foreach ($violations as $violation) {
                $this->addFlash('error', $violation->getMessage());
            }
            return $this->redirectToRoute('app_service_home');
        }

        // ========================================
        // 3. NETTOYAGE ET NORMALISATION
        // ========================================
        $search = trim($search);
        $pageHopital = max(1, (int) $pageHopital);
        $pageRdv = max(1, (int) $pageRdv);

        // Protection supplémentaire : échappement HTML pour éviter XSS
        $search = htmlspecialchars($search, ENT_QUOTES, 'UTF-8');

        // ========================================
        // 4. PARAMÈTRES DE PAGINATION
        // ========================================
        $itemsPerPage = 10;

        $hopitaux = [];
        $rendezvous = [];
        $totalHopitaux = 0;
        $totalRdv = 0;

        // ========================================
        // 5. LOGIQUE DE RECHERCHE ET FILTRAGE
        // ========================================
        try {
            if (!empty($search)) {
                // Recherche avec critères
                if ($filter === 'hopital' || $filter === 'all') {
                    $result = $hopitalRepository->searchPaginated(
                        $search, 
                        $pageHopital, 
                        $itemsPerPage,
                        $sortHopital
                    );
                    $hopitaux = $result['data'];
                    $totalHopitaux = $result['total'];
                }

                if ($filter === 'rdv' || $filter === 'all') {
                    $result = $rendezVousRepository->searchPaginated(
                        $search, 
                        $pageRdv, 
                        $itemsPerPage,
                        $sortRdv
                    );
                    $rendezvous = $result['data'];
                    $totalRdv = $result['total'];
                }
            } else {
                // Affichage sans recherche
                if ($filter === 'hopital' || $filter === 'all') {
                    $result = $hopitalRepository->findPaginated(
                        $pageHopital, 
                        $itemsPerPage,
                        $sortHopital
                    );
                    $hopitaux = $result['data'];
                    $totalHopitaux = $result['total'];
                }

                if ($filter === 'rdv' || $filter === 'all') {
                    $result = $rendezVousRepository->findPaginated(
                        $pageRdv, 
                        $itemsPerPage,
                        $sortRdv
                    );
                    $rendezvous = $result['data'];
                    $totalRdv = $result['total'];
                }
            }
        } catch (\Exception $e) {
            // Gestion des erreurs de base de données
            $this->addFlash('error', 'Une erreur est survenue lors de la récupération des données.');
            
            // Log l'erreur (en production)
            // $logger->error('Erreur dans ServiceHomeController: ' . $e->getMessage());
            
            return $this->redirectToRoute('app_service_home');
        }

        // ========================================
        // 6. CALCUL DU NOMBRE DE PAGES
        // ========================================
        $totalPagesHopital = $totalHopitaux > 0 ? (int) ceil($totalHopitaux / $itemsPerPage) : 1;
        $totalPagesRdv = $totalRdv > 0 ? (int) ceil($totalRdv / $itemsPerPage) : 1;

        // Vérification que les pages demandées ne dépassent pas le total
        if ($pageHopital > $totalPagesHopital) {
            return $this->redirectToRoute('app_service_home', [
                'q' => $search,
                'filter' => $filter,
                'sort_hopital' => $sortHopital,
                'sort_rdv' => $sortRdv,
                'page_hopital' => $totalPagesHopital,
                'page_rdv' => $pageRdv
            ]);
        }

        if ($pageRdv > $totalPagesRdv) {
            return $this->redirectToRoute('app_service_home', [
                'q' => $search,
                'filter' => $filter,
                'sort_hopital' => $sortHopital,
                'sort_rdv' => $sortRdv,
                'page_hopital' => $pageHopital,
                'page_rdv' => $totalPagesRdv
            ]);
        }

        // ========================================
        // 7. STATISTIQUES GLOBALES
        // ========================================
        $medecinCount = $userRepository->countMedecins();
        $patientCount = $userRepository->countPatients();
        $hopitalCount = $hopitalRepository->count([]);

        // ========================================
        // 8. RENDU DE LA VUE
        // ========================================
        return $this->render('Front_office/service_home/index.html.twig', [
            'hopitaux'            => $hopitaux,
            'rendezvous'          => $rendezvous,
            'medecin_count'       => $medecinCount,
            'patient_count'       => $patientCount,
            'hopital_count'       => $hopitalCount,
            'search'              => $search,
            'filter'              => $filter,
            'sort_hopital'        => $sortHopital,
            'sort_rdv'            => $sortRdv,
            'page_hopital'        => $pageHopital,
            'page_rdv'            => $pageRdv,
            'total_pages_hopital' => $totalPagesHopital,
            'total_pages_rdv'     => $totalPagesRdv,
            'total_hopitaux'      => $totalHopitaux,
            'total_rdv'           => $totalRdv,
        ]);
    }
}