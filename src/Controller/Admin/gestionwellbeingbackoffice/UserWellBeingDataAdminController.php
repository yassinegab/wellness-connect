<?php

namespace App\Controller\Admin\gestionwellbeingbackoffice;

use App\Entity\User;
use App\Entity\UserWellBeingData;
use App\Form\UserWellBeingDataType;
use App\Repository\UserWellBeingDataRepository;
use App\Service\StressPredictionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/well-being-data', name: 'app_user_well_being_data_')]
class UserWellBeingDataAdminController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(Request $request, UserWellBeingDataRepository $repository): Response
    {
        $searchTerm = $request->query->get('search');
        $sortField = $request->query->get('sortField', 'createdAt');
        $sortDirection = $request->query->get('sortDirection', 'desc');

        $sort = [$sortField => $sortDirection];
        $userWellBeingDatas = $repository->findBySearchAndSort($searchTerm, $sort);
        $stats = $repository->getStatistics($searchTerm);

        return $this->render('admin/gestionwellbeingbackoffice/user_well_being_data/index.html.twig', [
            'user_well_being_datas' => $userWellBeingDatas,
            'stats' => $stats,
            'searchTerm' => $searchTerm,
            'sortField' => $sortField,
            'sortDirection' => $sortDirection,
        ]);
    }

    #[Route('/charts', name: 'charts', methods: ['GET'])]
    public function charts(UserWellBeingDataRepository $repository): Response
    {
        $stats = $repository->getStatistics();

        return $this->render('admin/gestionwellbeingbackoffice/user_well_being_data/charts.html.twig', [
            'stats' => $stats,
        ]);
    }

    #[Route('/pdf', name: 'pdf', methods: ['GET'])]
    public function pdf(UserWellBeingDataRepository $repository): Response
    {
        $datas = $repository->findAll();

        return $this->render('admin/gestionwellbeingbackoffice/user_well_being_data/pdf.html.twig', [
            'datas' => $datas,
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, StressPredictionService $predictionService): Response
    {
        $userWellBeingDatum = new UserWellBeingData();
        $form = $this->createForm(UserWellBeingDataType::class, $userWellBeingDatum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Admin can assign user (default to admin or first user for now)
            if (!$userWellBeingDatum->getUser()) {
                $user = $this->getUser() ?? $entityManager->getRepository(User::class)->find(1);
                $userWellBeingDatum->setUser($user);
            }

            $entityManager->persist($userWellBeingDatum);

            // Generate Prediction
            $prediction = $predictionService->predict($userWellBeingDatum);
            $entityManager->persist($prediction);

            $entityManager->flush();

            return $this->redirectToRoute('app_user_well_being_data_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/gestionwellbeingbackoffice/user_well_being_data/new.html.twig', [
            'user_well_being_datum' => $userWellBeingDatum,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(UserWellBeingData $userWellBeingDatum): Response
    {
        return $this->render('admin/gestionwellbeingbackoffice/user_well_being_data/show.html.twig', [
            'user_well_being_datum' => $userWellBeingDatum,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, UserWellBeingData $userWellBeingDatum, EntityManagerInterface $entityManager, StressPredictionService $predictionService): Response
    {
        $form = $this->createForm(UserWellBeingDataType::class, $userWellBeingDatum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Regenerate Prediction on edit
            $prediction = $predictionService->predict($userWellBeingDatum);
            $entityManager->persist($prediction);

            $entityManager->flush();

            return $this->redirectToRoute('app_user_well_being_data_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/gestionwellbeingbackoffice/user_well_being_data/edit.html.twig', [
            'user_well_being_datum' => $userWellBeingDatum,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, UserWellBeingData $userWellBeingDatum, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $userWellBeingDatum->getId(), $request->request->get('_token'))) {
            $entityManager->remove($userWellBeingDatum);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_well_being_data_index', [], Response::HTTP_SEE_OTHER);
    }
}
