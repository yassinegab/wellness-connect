<?php

namespace App\Controller\Admin\gestionwellbeingbackoffice;

use App\Entity\Meal;
use App\Entity\User;
use App\Form\MealType;
use App\Repository\MealRepository;
use App\Service\QwenService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/meal', name: 'app_meal_')]
class MealAdminController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(Request $request, MealRepository $repository): Response
    {
        $searchTerm = $request->query->get('search');
        $sortField = $request->query->get('sortField', 'createAt');
        $sortDirection = $request->query->get('sortDirection', 'desc');

        $meals = $repository->findBySearchAndSort($searchTerm, $sortField, $sortDirection);
        $stats = $repository->getStatistics();

        return $this->render('admin/gestionwellbeingbackoffice/meal/index.html.twig', [
            'meals' => $meals,
            'stats' => $stats,
            'searchTerm' => $searchTerm,
            'sortField' => $sortField,
            'sortDirection' => $sortDirection,
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, QwenService $qwenService): Response
    {
        $meal = new Meal();
        $user = $this->getUser();
        // If no user is logged in (which shouldn't happen in admin), or if we want to fallback
        if (!$user) {
            $user = $entityManager->getRepository(User::class)->find(1);
        }
        $meal->setUser($user);
        $meal->setCreateAt(new \DateTimeImmutable());

        $form = $this->createForm(MealType::class, $meal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageName')->getData();

            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();
                $targetDir = $this->getParameter('meal_images_directory');

                try {
                    $imageFile->move($targetDir, $newFilename);
                    $meal->setImageName($newFilename);

                    // AI Analysis
                    $fullPath = $targetDir . '/' . $newFilename;
                    $analysis = $qwenService->analyzeMeal($fullPath, $meal->getDescription());
                    $meal->setAiAnalysis($analysis);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Image upload failed: ' . $e->getMessage());
                }
            }

            $entityManager->persist($meal);
            $entityManager->flush();

            return $this->redirectToRoute('app_meal_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/gestionwellbeingbackoffice/meal/new.html.twig', [
            'meal' => $meal,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Meal $meal): Response
    {
        return $this->render('admin/gestionwellbeingbackoffice/meal/show.html.twig', [
            'meal' => $meal,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Meal $meal, EntityManagerInterface $entityManager, QwenService $qwenService): Response
    {
        $form = $this->createForm(MealType::class, $meal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageName')->getData();

            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();
                $targetDir = $this->getParameter('meal_images_directory');

                try {
                    $imageFile->move($targetDir, $newFilename);
                    $meal->setImageName($newFilename);

                    // AI Analysis
                    $fullPath = $targetDir . '/' . $newFilename;
                    $analysis = $qwenService->analyzeMeal($fullPath, $meal->getDescription());
                    $meal->setAiAnalysis($analysis);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Image upload failed: ' . $e->getMessage());
                }
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_meal_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/gestionwellbeingbackoffice/meal/edit.html.twig', [
            'meal' => $meal,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Meal $meal, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $meal->getId(), $request->request->get('_token'))) {
            $entityManager->remove($meal);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_meal_index', [], Response::HTTP_SEE_OTHER);
    }
}
