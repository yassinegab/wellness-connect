<?php

namespace App\Controller\Admin;

use App\Entity\Front_office\RendezVous;
use App\Form\RendezVousType;
use App\Repository\RendezVousRepository;        // ✅ CORRECTION ICI
use App\Repository\HopitalRepository;           // ✅ CORRECTION ICI
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/rendez-vous')]
class RendezVousController extends AbstractController
{
    #[Route('', name: 'admin_rendez_vous_index', methods: ['GET'])]
    public function index(
        Request $request,
        RendezVousRepository $rendezVousRepository,
        HopitalRepository $hopitalRepository
    ): Response {
        // Récupération recherche + filtre
        $search = $request->query->get('q', '');
        $filter = $request->query->get('filter', 'all');

        $rendezVous = [];
        $hopitaux = [];

        if ($search) {
            if ($filter === 'rdv') {
                // Rechercher uniquement dans les rendez-vous
                $rendezVous = $rendezVousRepository->search($search);
                $hopitaux = [];
            } elseif ($filter === 'hopital') {
                // Rechercher uniquement dans les hôpitaux
                $rendezVous = [];
                $hopitaux = $hopitalRepository->search($search);
            } else {
                // Rechercher dans les deux
                $rendezVous = $rendezVousRepository->search($search);
                $hopitaux = $hopitalRepository->search($search);
            }
        } else {
            // Afficher tout
            $rendezVous = $rendezVousRepository->findAll();
            $hopitaux = $hopitalRepository->findAll();
        }

        return $this->render('admin/rendez_vous/index.html.twig', [
            'rendez_vous' => $rendezVous,
            'hopitaux' => $hopitaux,
            'search' => $search,
            'filter' => $filter,
        ]);
    }

    #[Route('/new', name: 'admin_rendez_vous_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $rendezVous = new RendezVous();
        $form = $this->createForm(RendezVousType::class, $rendezVous);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($rendezVous);
            $entityManager->flush();

            $this->addFlash('success', 'Rendez-vous créé avec succès !');

            return $this->redirectToRoute('admin_rendez_vous_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/rendez_vous/new.html.twig', [
            'rendez_vous' => $rendezVous,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_rendez_vous_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(RendezVous $rendezVous): Response
    {
        return $this->render('admin/rendez_vous/show.html.twig', [
            'rendez_vous' => $rendezVous,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_rendez_vous_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Request $request, RendezVous $rendezVous, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RendezVousType::class, $rendezVous);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Rendez-vous modifié avec succès !');

            return $this->redirectToRoute('admin_rendez_vous_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/rendez_vous/edit.html.twig', [
            'rendez_vous' => $rendezVous,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_rendez_vous_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Request $request, RendezVous $rendezVous, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$rendezVous->getId(), $request->request->get('_token'))) {
            $entityManager->remove($rendezVous);
            $entityManager->flush();
            
            $this->addFlash('success', 'Rendez-vous supprimé avec succès !');
        } else {
            $this->addFlash('error', 'Token CSRF invalide. Suppression annulée.');
        }

        return $this->redirectToRoute('admin_rendez_vous_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/statistiques', name: 'admin_rendez_vous_stats', methods: ['GET'])]
    public function statistiques(RendezVousRepository $rendezVousRepository): Response
    {
        $stats = [
            'total' => $rendezVousRepository->count([]),
            'en_attente' => $rendezVousRepository->count(['statut' => 'En attente']),
            'confirme' => $rendezVousRepository->count(['statut' => 'Confirmé']),
            'annule' => $rendezVousRepository->count(['statut' => 'Annulé']),
            'termine' => $rendezVousRepository->count(['statut' => 'Terminé']),
        ];

        return $this->render('admin/rendez_vous/statistiques.html.twig', [
            'stats' => $stats,
        ]);
    }

    #[Route('/{id}/confirmer', name: 'admin_rendez_vous_confirmer', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function confirmer(Request $request, RendezVous $rendezVous, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('confirmer'.$rendezVous->getId(), $request->request->get('_token'))) {
            $rendezVous->setStatut('Confirmé');
            $entityManager->flush();
            
            $this->addFlash('success', 'Rendez-vous confirmé avec succès !');
        } else {
            $this->addFlash('error', 'Token CSRF invalide.');
        }

        return $this->redirectToRoute('admin_rendez_vous_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/annuler', name: 'admin_rendez_vous_annuler', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function annuler(Request $request, RendezVous $rendezVous, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('annuler'.$rendezVous->getId(), $request->request->get('_token'))) {
            $rendezVous->setStatut('Annulé');
            $entityManager->flush();
            
            $this->addFlash('success', 'Rendez-vous annulé avec succès !');
        } else {
            $this->addFlash('error', 'Token CSRF invalide.');
        }

        return $this->redirectToRoute('admin_rendez_vous_index', [], Response::HTTP_SEE_OTHER);
    }
}