<?php

namespace App\Controller;

use App\Entity\Meal;
use App\Form\MealType;
use App\Repository\MealRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/meal')]
final class MealController extends AbstractController
{
    #[Route(name: 'app_meal_index', methods: ['GET'])]
    public function index(MealRepository $mealRepository, Request $request): Response
    {
        // Get query parameters, provide defaults
        $searchTerm = $request->query->get('search', '');
        $sortField = $request->query->get('sortField', 'id');       // default sort field
        $sortDirection = $request->query->get('sortDirection', 'asc'); // default direction

        // Fetch meals from repository
        $meals = $mealRepository->findBySearchAndSort($searchTerm, $sortField, $sortDirection);

        $meals = $mealRepository->findAll();

        $stats = [
            'totalMeals' => count($meals),
            'withImage' => count(array_filter($meals, fn($m) => $m->getImageName() !== null)),
            'withoutImage' => count(array_filter($meals, fn($m) => $m->getImageName() === null)),
            'avgDescriptionLength' => $meals ? array_sum(array_map(fn($m) => strlen($m->getDescription()), $meals)) / count($meals) : 0,
        ];

        return $this->render('meal/index.html.twig', [
            'meals' => $meals,
            'stats' => $stats,
            'searchTerm' => $searchTerm,
            'sortField' => $sortField,
            'sortDirection' => $sortDirection,
        ]);
    }
    #[Route('/new', name: 'app_meal_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, \App\Service\QwenService $qwenService): Response
    {
        $meal = new Meal();
        $form = $this->createForm(MealType::class, $meal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $imageFile = $form->get('imageName')->getData();

            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();
                $targetDir = $this->getParameter('meal_images_directory');

                try {
                    $imageFile->move(
                        $targetDir,
                        $newFilename
                    );
                    $meal->setImageName($newFilename);

                    // --- AI Analysis ---
                    $fullPath = $targetDir . '/' . $newFilename;
                    $analysis = $qwenService->analyzeMeal($fullPath, $meal->getDescription());
                    $meal->setAiAnalysis($analysis);

                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                    $this->addFlash('error', 'Failed to upload image: ' . $e->getMessage());
                }
            } else {
                 $this->addFlash('warning', 'Please upload an image for AI analysis.');
                 // Optionally prevent saving if image is mandatory for analysis
            }

            $entityManager->persist($meal);
            $entityManager->flush();

            return $this->redirectToRoute('app_meal_index');
        }


        return $this->render('meal/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_meal_show', methods: ['GET'])]
    public function show(Meal $meal): Response
    {
        return $this->render('meal/show.html.twig', [
            'meal' => $meal,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_meal_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Meal $meal, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MealType::class, $meal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_meal_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('meal/edit.html.twig', [
            'meal' => $meal,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_meal_delete', methods: ['POST'])]
    public function delete(Request $request, Meal $meal, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $meal->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($meal);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_meal_index', [], Response::HTTP_SEE_OTHER);
    }
}
