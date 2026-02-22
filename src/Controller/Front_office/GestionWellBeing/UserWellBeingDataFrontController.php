<?php
// src/Controller/UserWellBeingDataController.php
namespace App\Controller\Front_office\GestionWellBeing;

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
use App\Repository\MealRepository;
use App\Repository\JournalRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

#[Route('/wellbeing', name: 'user_wellbeing_')]
class UserWellBeingDataFrontController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(
        UserWellBeingDataRepository $repo,
        MealRepository $mealRepo,
        JournalRepository $journalRepo,
        EntityManagerInterface $em,
        \App\Service\StressPredictionService $predictionService
    ): Response {
        // Get the logged-in user
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // 1. Fetch Current User Data
        $userData = $repo->findBy(['user' => $user], ['createdAt' => 'DESC']);
        $userMeals = $mealRepo->findBy(['user' => $user], ['createAt' => 'DESC']);
        $userJournals = $journalRepo->findBy(['user' => $user], ['createdAt' => 'DESC']);

        // 2. AI Trend Interpretation
        $aiTrends = $predictionService->interpretTrends($user);

        return $this->render('user_wellbeing/index.html.twig', [
            'dataList' => $userData,
            'meals' => $userMeals,
            'journals' => $userJournals,
            'aiTrends' => $aiTrends,
        ]);
    }

    #[Route('/statistics', name: 'statistics')]
    public function statistics(
        UserWellBeingDataRepository $repo,
        MealRepository $mealRepo,
        JournalRepository $journalRepo,
        \App\Repository\StressPredictionRepository $predictRepo,
        EntityManagerInterface $em,
        ChartBuilderInterface $chartBuilder
    ): Response {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // 1. Fetch Current User Data
        $predictions = $predictRepo->findAllForUser($user);
        $data = $repo->findBy(['user' => $user], ['createdAt' => 'ASC']);
        $meals = $mealRepo->findBy(['user' => $user], ['createAt' => 'ASC']);

        // 1. Mood Chart (latest 7 entries)
        $moodChart = $chartBuilder->createChart(Chart::TYPE_LINE);
        $moodData = array_slice($data, -7);
        $moodChart->setData([
            'labels' => array_map(fn($d) => $d->getCreatedAt()->format('M d'), $moodData),
            'datasets' => [
                [
                    'label' => 'Anxiety/Tension Level',
                    'backgroundColor' => 'rgba(99, 102, 241, 0.2)',
                    'borderColor' => 'rgb(99, 102, 241)',
                    'data' => array_map(fn($d) => $d->getAnxietyTension(), $moodData),
                    'tension' => 0.4,
                ],
            ],
        ]);
        $moodChart->setOptions([
            'scales' => ['y' => ['min' => 0, 'max' => 5]]
        ]);

        // 2. Calories Chart
        $calData = [];
        foreach ($meals as $meal) {
            $date = $meal->getCreateAt()->format('Y-m-d');
            $calData[$date] = ($calData[$date] ?? 0) + ($meal->getCalories() ?: 0);
        }
        $calChart = $chartBuilder->createChart(Chart::TYPE_BAR);
        $calChart->setData([
            'labels' => array_keys($calData),
            'datasets' => [
                [
                    'label' => 'Daily Calories',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.5)',
                    'borderColor' => 'rgb(16, 185, 129)',
                    'data' => array_values($calData),
                ],
            ],
        ]);

        // 3. Activity Chart (Logs frequency)
        $activityData = [];
        foreach ($data as $d) {
            $date = $d->getCreatedAt()->format('Y-m-d');
            $activityData[$date] = ($activityData[$date] ?? 0) + 1;
        }
        $activityChart = $chartBuilder->createChart(Chart::TYPE_LINE);
        $activityChart->setData([
            'labels' => array_keys($activityData),
            'datasets' => [
                [
                    'label' => 'Logs per Day',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.2)',
                    'borderColor' => 'rgb(245, 158, 11)',
                    'data' => array_values($activityData),
                    'fill' => true,
                ],
            ],
        ]);

        // 4. Stress Trend Chart
        $stressChart = $chartBuilder->createChart(Chart::TYPE_LINE);
        $stressChart->setData([
            'labels' => array_map(fn($p) => $p->getCreatedAt()->format('M d, H:i'), $predictions),
            'datasets' => [
                [
                    'label' => 'Stress Score (%)',
                    'backgroundColor' => 'rgba(99, 102, 241, 0.1)',
                    'borderColor' => '#6366F1',
                    'data' => array_map(fn($p) => $p->getConfidenceScore(), $predictions),
                    'tension' => 0.4,
                    'fill' => true,
                ],
            ],
        ]);
        $stressChart->setOptions(['scales' => ['y' => ['min' => 0, 'max' => 100]]]);

        return $this->render('user_wellbeing/statistics.html.twig', [
            'predictions' => $predictions,
            'moodChart' => $moodChart,
            'calChart' => $calChart,
            'activityChart' => $activityChart,
            'stressChart' => $stressChart,
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
