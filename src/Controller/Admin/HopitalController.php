<?php

namespace App\Controller\Admin;

use App\Entity\Front_office\Hopital;
use App\Form\HopitalType;
use App\Repository\HopitalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/hopital')]
class HopitalController extends AbstractController
{
    #[Route('/', name: 'admin_hopital_index', methods: ['GET'])]
    public function index(HopitalRepository $hopitalRepository): Response
    {
        return $this->render('admin/hopital/index.html.twig', [
            'hopitaux' => $hopitalRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'admin_hopital_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $hopital = new Hopital();
        $form = $this->createForm(HopitalType::class, $hopital);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $entityManager->persist($hopital);
                $entityManager->flush();

                $this->addFlash('success', 'Hôpital créé avec succès !');

                return $this->redirectToRoute('admin_rendez_vous_index', [], Response::HTTP_SEE_OTHER);
            }

            // 👉 formulaire soumis mais invalide (normal)
            $this->addFlash('error', 'Veuillez corriger les erreurs du formulaire.');
        }

        return $this->render('admin/hopital/new.html.twig', [
            'hopital' => $hopital,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_hopital_show', methods: ['GET'])]
    public function show(Hopital $hopital = null): Response
    {
        if (!$hopital) {
            $this->addFlash('error', 'Hôpital introuvable.');
            return $this->redirectToRoute('admin_hopital_index');
        }

        return $this->render('admin/hopital/show.html.twig', [
            'hopital' => $hopital,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_hopital_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Hopital $hopital = null, EntityManagerInterface $entityManager): Response
    {
        if (!$hopital) {
            $this->addFlash('error', 'Hôpital introuvable.');
            return $this->redirectToRoute('admin_hopital_index');
        }

        $form = $this->createForm(HopitalType::class, $hopital);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $entityManager->flush();

                $this->addFlash('success', 'Hôpital modifié avec succès !');

                return $this->redirectToRoute('admin_rendez_vous_index', [], Response::HTTP_SEE_OTHER);
            }

            $this->addFlash('error', 'Veuillez corriger les erreurs du formulaire.');
        }

        return $this->render('admin/hopital/edit.html.twig', [
            'hopital' => $hopital,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_hopital_delete', methods: ['POST'])]
    public function delete(Request $request, Hopital $hopital = null, EntityManagerInterface $entityManager): Response
    {
        if (!$hopital) {
            $this->addFlash('error', 'Hôpital introuvable.');
            return $this->redirectToRoute('admin_rendez_vous_index');
        }

        if ($this->isCsrfTokenValid('delete'.$hopital->getId(), $request->request->get('_token'))) {
            $entityManager->remove($hopital);
            $entityManager->flush();

            $this->addFlash('success', 'Hôpital supprimé avec succès !');
        }

        return $this->redirectToRoute('admin_rendez_vous_index', [], Response::HTTP_SEE_OTHER);
    }
}
