<?php

namespace App\Controller;

use App\Entity\AnalyseAI;
use App\Form\AnalyseAIType;
use App\Repository\AnalyseAIRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/analyse/a/i')]
final class AnalyseAIController extends AbstractController
{
    #[Route(name: 'app_analyse_a_i_index', methods: ['GET'])]
    public function index(AnalyseAIRepository $analyseAIRepository): Response
    {
        return $this->render('analyse_ai/index.html.twig', [
            'analyse_a_is' => $analyseAIRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_analyse_a_i_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $analyseAI = new AnalyseAI();
        $form = $this->createForm(AnalyseAIType::class, $analyseAI);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($analyseAI);
            $entityManager->flush();

            return $this->redirectToRoute('app_analyse_a_i_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('analyse_ai/new.html.twig', [
            'analyse_a_i' => $analyseAI,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_analyse_a_i_show', methods: ['GET'])]
    public function show(AnalyseAI $analyseAI): Response
    {
        return $this->render('analyse_ai/show.html.twig', [
            'analyse_a_i' => $analyseAI,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_analyse_a_i_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, AnalyseAI $analyseAI, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AnalyseAIType::class, $analyseAI);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_analyse_a_i_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('analyse_ai/edit.html.twig', [
            'analyse_a_i' => $analyseAI,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_analyse_a_i_delete', methods: ['POST'])]
    public function delete(Request $request, AnalyseAI $analyseAI, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$analyseAI->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($analyseAI);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_analyse_a_i_index', [], Response::HTTP_SEE_OTHER);
    }
}
