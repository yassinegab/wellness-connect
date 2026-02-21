<?php

namespace App\Service;

use App\Entity\ChatbotMessage;
use App\Entity\User;
use App\Repository\ChatbotMessageRepository;
use App\Repository\UserWellBeingDataRepository;
use Doctrine\ORM\EntityManagerInterface;

class ChatbotService
{
    private QwenService $qwenService;
    private ChatbotMessageRepository $messageRepository;
    private \App\Repository\MealRepository $mealRepository;
    private \App\Repository\JournalRepository $journalRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        QwenService $qwenService,
        ChatbotMessageRepository $messageRepository,
        UserWellBeingDataRepository $wellBeingRepository,
        \App\Repository\MealRepository $mealRepository,
        \App\Repository\JournalRepository $journalRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->qwenService = $qwenService;
        $this->messageRepository = $messageRepository;
        $this->wellBeingRepository = $wellBeingRepository;
        $this->mealRepository = $mealRepository;
        $this->journalRepository = $journalRepository;
        $this->entityManager = $entityManager;
    }

    public function getResponse(User $user, string $userMessage): string
    {
        // 1. Get user context
        $latestWellbeing = $this->wellBeingRepository->findOneBy(['user' => $user], ['createdAt' => 'DESC']);
        $latestMeal = $this->mealRepository->findOneBy(['user' => $user], ['createAt' => 'DESC']);
        $latestJournal = $this->journalRepository->findOneBy(['user' => $user], ['createdAt' => 'DESC']);
        
        $context = "You are a Mental Health & Wellness Coach. You ONLY discuss topics related to:
        - Mental health (stress, anxiety, depression, burnout, emotional wellbeing)
        - Sleep quality and sleep hygiene
        - Relaxation techniques and Sport recommendations
        - Work-life balance and academic stress
        
        SPORT RECOMMENDATION RULES (If the user asks for activities):
        - Recommend 2 to 4 sport activities with Duration (min) and a short explanation.
        - If stress is HIGH -> suggest calming activities (yoga, walking, breathing).
        - If stress is MEDIUM -> suggest moderate intensity (cycling, light jogging).
        - If stress is LOW and mood is positive -> suggest higher intensity (HIIT, running, gym).
        - If latest meal was heavy -> suggest light activity.
        - If latest meal was light/balanced -> suggest moderate/high intensity.
        - If mood is SAD/ANXIOUS -> prioritize stress-reducing and dopamine-boosting activities.
        - If mood is ANGRY -> suggest energy-releasing activities.
        - If mood is TIRED -> suggest gentle mobility exercises.
        
        STRICT RULES:
        1. If the user asks about ANYTHING outside mental health and wellness (e.g. coding, math, general knowledge), you MUST politely decline.
        2. Always include a brief disclaimer that you provide guidance, not medical advice.
        3. Respond in the same language the user writes in.
        
        Current User Data:";
        
        if ($latestWellbeing) {
            $context .= "
            - Wellbeing Score (Anxiety/Tension): " . $latestWellbeing->getAnxietyTension() . " (1-5)";
        }
        
        if ($latestMeal) {
            $context .= "
            - Latest Meal: " . $latestMeal->getDescription() . " (" . $latestMeal->getCalories() . " kcal)";
        }
        
        if ($latestJournal) {
            $context .= "
            - Latest Journal Mood: " . $latestJournal->getDetectedEmotion();
        }

        if (!$latestWellbeing && !$latestMeal && !$latestJournal) {
            $context .= " No specific records found yet.";
        }

        // 2. Get recent chat history
        $history = $this->messageRepository->findBy(['user' => $user], ['createdAt' => 'ASC'], 10);
        
        $messages = [
            ['role' => 'system', 'content' => $context]
        ];

        foreach ($history as $msg) {
            $messages[] = ['role' => $msg->getRole(), 'content' => $msg->getContent()];
        }

        $messages[] = ['role' => 'user', 'content' => $userMessage];

        // 3. Call AI
        $response = $this->qwenService->getChatCompletion($messages);

        return $response;
    }

    public function saveMessage(User $user, string $content, string $role): ChatbotMessage
    {
        $message = new ChatbotMessage();
        $message->setUser($user);
        $message->setContent($content);
        $message->setRole($role);
        
        $this->entityManager->persist($message);
        $this->entityManager->flush();
        
        return $message;
    }
}
