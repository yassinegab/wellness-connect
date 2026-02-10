<?php

namespace App\Controller;

use App\Entity\UserWellBeingData;
use App\Form\UserWellBeingData2Type;
use App\Repository\UserWellBeingDataRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/user/well/being/data')]
final class UserWellBeingDataController extends AbstractController
{
    #[Route(name: 'app_user_well_being_data_index', methods: ['GET'])]
    public function index(Request $request, UserWellBeingDataRepository $repo): Response
    {
        $term = $request->query->get('search', null);
        $sortField = $request->query->get('sortField', 'createdAt');
        $sortDirection = strtolower($request->query->get('sortDirection', 'desc')) === 'asc' ? 'ASC' : 'DESC';

        $userWellBeingDatas = $repo->findBySearchAndSort($term, [$sortField => $sortDirection]);

        // Call the repository method to get stats
        $stats = $repo->getStatistics($term);

        return $this->render('user_well_being_data/index.html.twig', [
            'user_well_being_datas' => $userWellBeingDatas,
            'searchTerm' => $term,
            'sortField' => $sortField,
            'sortDirection' => strtolower($sortDirection),
            'stats' => $stats,
        ]);
    }



    #[Route('/new', name: 'app_user_well_being_data_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $userWellBeingDatum = new UserWellBeingData();
        $form = $this->createForm(UserWellBeingData2Type::class, $userWellBeingDatum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($userWellBeingDatum);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_well_being_data_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user_well_being_data/new.html.twig', [
            'user_well_being_datum' => $userWellBeingDatum,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_well_being_data_show', methods: ['GET'])]
    public function show(UserWellBeingData $userWellBeingDatum): Response
    {
        return $this->render('user_well_being_data/show.html.twig', [
            'user_well_being_datum' => $userWellBeingDatum,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_well_being_data_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, UserWellBeingData $userWellBeingDatum, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserWellBeingData2Type::class, $userWellBeingDatum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_user_well_being_data_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user_well_being_data/edit.html.twig', [
            'user_well_being_datum' => $userWellBeingDatum,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_well_being_data_delete', methods: ['POST'])]
    public function delete(Request $request, UserWellBeingData $userWellBeingDatum, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $userWellBeingDatum->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($userWellBeingDatum);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_well_being_data_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/user-well-being/charts', name: 'app_user_well_being_charts', methods: ['GET'])]
    public function charts(UserWellBeingDataRepository $repo): Response
    {
        $stats = $repo->getStatistics(); // your repo function that calculates averages, counts, etc.

        return $this->render('user_well_being_data/charts.html.twig', [
            'stats' => $stats,
        ]);
    }
    
}
