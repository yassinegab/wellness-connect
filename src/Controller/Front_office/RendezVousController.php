<?php

namespace App\Controller\Front_office;

use App\Entity\Front_office\RendezVous;
use App\Form\RendezVousType;
use App\Repository\Front_office\RendezVousRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/rendez-vous')]
class RendezVousController extends AbstractController
{
    #[Route('', name: 'app_rendez_vous_index', methods: ['GET'])]
    public function index(RendezVousRepository $rendezVousRepository): Response
    {
        return $this->render('Front_office/rendez_vous/index.html.twig', [
            'rendez_vous' => $rendezVousRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_rendez_vous_new', methods: ['GET', 'POST'], priority: 10)]
public function new(Request $request, EntityManagerInterface $entityManager): Response
{
    $rendezVous = new RendezVous();
    $form = $this->createForm(RendezVousType::class, $rendezVous);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

        if ($rendezVous->getDateRendezVous() instanceof \DateTimeImmutable) {
            $rendezVous->setDateRendezVous(
                \DateTime::createFromImmutable($rendezVous->getDateRendezVous())
            );
        }

        $entityManager->persist($rendezVous);
        $entityManager->flush();

        $this->addFlash('success', 'Rendez-vous créé avec succès !');

        return $this->redirectToRoute('app_rendez_vous_index');
    }

    return $this->render('Front_office/rendez_vous/new.html.twig', [
        'rendez_vous' => $rendezVous,
        'form' => $form,
    ]);
}


    #[Route('/{id}/edit', name: 'app_rendez_vous_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'], priority: 5)]
    public function edit(Request $request, RendezVous $rendezVous, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RendezVousType::class, $rendezVous);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Rendez-vous modifié avec succès !');

            return $this->redirectToRoute('app_rendez_vous_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Front_office/rendez_vous/edit.html.twig', [
            'rendez_vous' => $rendezVous,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_rendez_vous_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(RendezVous $rendezVous): Response
    {
        return $this->render('Front_office/rendez_vous/show.html.twig', [
            'rendez_vous' => $rendezVous,
        ]);
    }

    #[Route('/{id}', name: 'app_rendez_vous_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Request $request, RendezVous $rendezVous, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$rendezVous->getId(), $request->request->get('_token'))) {
            $entityManager->remove($rendezVous);
            $entityManager->flush();
            
            $this->addFlash('success', 'Rendez-vous supprimé avec succès !');
        }

        return $this->redirectToRoute('app_rendez_vous_index', [], Response::HTTP_SEE_OTHER);
    }
}