<?php
// src/Controller/UserWellBeingDataController.php
namespace App\Controller\Front_office;

use App\Entity\UserWellBeingData;
use App\Form\UserWellBeingDataType;
use App\Repository\UserWellBeingDataRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;

#[Route('/wellbeing', name: 'user_wellbeing_')]
class UserWellBeingDataFrontController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(UserWellBeingDataRepository $repo, \App\Repository\MealRepository $mealRepo, EntityManagerInterface $em): Response
    {
        // Try to get the logged-in user, otherwise fallback to user 1 for testing
        $user = $this->getUser() ?? $em->getRepository(User::class)->find(1);

        if (!$user) {
             return $this->render('user_wellbeing/index.html.twig', [
                'dataList' => [],
                'meals' => [],
            ]);
        }

        $data = $repo->findBy(['user' => $user], ['createdAt' => 'DESC']);
        $meals = $mealRepo->findBy(['user' => $user], ['createAt' => 'DESC']);

        return $this->render('user_wellbeing/index.html.twig', [
            'dataList' => $data,
            'meals' => $meals,
        ]);
    }

    #[Route('/statistics', name: 'statistics')]
    public function statistics(\App\Repository\StressPredictionRepository $predictRepo, EntityManagerInterface $em): Response
    {
        $user = $this->getUser() ?? $em->getRepository(User::class)->find(1);
        
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $predictions = $predictRepo->findAllForUser($user);

        return $this->render('user_wellbeing/statistics.html.twig', [
            'predictions' => $predictions,
        ]);
    }

   #[Route('/new', name: 'new')]
public function new(Request $request, EntityManagerInterface $em, \App\Service\StressPredictionService $predictionService): Response
{
    $uwbData = new UserWellBeingData();

    $form = $this->createForm(UserWellBeingDataType::class, $uwbData);
    $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Assign user only if not already set (though handleRequest shouldn't set it)
            if (!$uwbData->getUser()) {
                 // Double check user fallback
                 $user = $this->getUser() ?? $em->getRepository(User::class)->find(1);
                 if ($user) {
                     $uwbData->setUser($user);
                 }
            }
            
            // Final safety check
            if (!$uwbData->getUser()) {
                 // Try to fallback to user 1 again if somehow lost
                 $fallbackUser = $em->getRepository(User::class)->find(1);
                 if ($fallbackUser) {
                     $uwbData->setUser($fallbackUser);
                 } else {
                     throw new \Exception('No user found. Please ensure User ID 1 exists.');
                 }
            }

            $em->persist($uwbData);
            
            // Generate Prediction
            $prediction = $predictionService->predict($uwbData);
            $em->persist($prediction);
            
            $em->flush();

            return $this->redirectToRoute('user_wellbeing_index');
        }

    return $this->render('user_wellbeing/new.html.twig', [
        'form' => $form->createView(),
    ]);
}



    #[Route('/{id}/edit', name: 'edit')]
    public function edit(Request $request, UserWellBeingData $uwbData, EntityManagerInterface $em, UserRepository $userRepo, \App\Service\StressPredictionService $predictionService): Response
    {
        // Try to get the logged-in user, otherwise fallback to user 1 for testing
        $user = $this->getUser() ?? $userRepo->find(1);
        
        if (!$user) {
             throw $this->createNotFoundException('No user found.');
        }

        $uwbData->setUser($user);

        $form = $this->createForm(UserWellBeingDataType::class, $uwbData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            // Regenerate Prediction on edit
            $prediction = $predictionService->predict($uwbData);
            $em->persist($prediction);

            $em->flush();
            $this->addFlash('success', 'Data updated successfully!');
            return $this->redirectToRoute('user_wellbeing_index');
        }

        return $this->render('user_wellbeing/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(
        Request $request,
        UserWellBeingData $data,
        EntityManagerInterface $entityManager
    ): RedirectResponse {
        // Check CSRF token
        if ($this->isCsrfTokenValid('delete' . $data->getId(), $request->request->get('_token'))) {
            $entityManager->remove($data);
            $entityManager->flush();

            $this->addFlash('success', 'Data deleted successfully!');
        }

        return $this->redirectToRoute('user_wellbeing_index');
    }
}
