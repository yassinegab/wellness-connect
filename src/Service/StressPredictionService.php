<?php

namespace App\Service;

use App\Entity\StressPrediction;
use App\Entity\UserWellBeingData;
use Doctrine\ORM\EntityManagerInterface;

class StressPredictionService
{
    private QwenService $qwenService;
    private EntityManagerInterface $entityManager;

    public function __construct(
        QwenService $qwenService, 
        EntityManagerInterface $entityManager,
        \App\Repository\UserWellBeingDataRepository $wellBeingRepository
    ) {
        $this->qwenService = $qwenService;
        $this->entityManager = $entityManager;
        $this->wellBeingRepository = $wellBeingRepository;
    }

    public function predict(UserWellBeingData $data): StressPrediction
    {
        // 1. Calculate Score (0-100)
        // Traits and their weights (simplified model)
        // High scores in these fields usually mean HIGHER stress
        $negativeFactors = [
            'sleepProblems' => 1.5,
            'headaches' => 2.0,
            'restlessness' => 1.5,
            'heartbeatPalpitations' => 2.0,
            'anxietyTension' => 2.5,
            'irritability' => 1.5,
        ];

        // High scores in these fields usually mean LOWER stress
        $positiveFactors = [
            'workEnvironment' => 1.5,
            'classAttendance' => 1.0,
            'lowAcademicConfidence' => -1.5, // Note: "Low confidence" actually means negative if high value
            'subjectConfidence' => 2.0,
        ];

        $totalScore = 0;
        $maxPossibleRaw = 0;

        foreach ($negativeFactors as $field => $weight) {
            $getter = 'get' . ucfirst($field);
            $val = $data->$getter();
            $totalScore += ($val * $weight);
            $maxPossibleRaw += (5 * $weight);
        }

        foreach ($positiveFactors as $field => $weight) {
             $getter = 'get' . ucfirst($field);
             $val = $data->$getter();
             // Invert positive factors (5 = low stress, 1 = high stress)
             // Score contribution: we want high score if stress is high
             // So for positive factor, 1 (bad) should contribute more to stress? 
             // Actually let's rethink: 
             // If Subject Confidence is 5 (Good), it should reduce stress.
             // If Subject Confidence is 1 (Bad), it should increase stress.
             
             // Simple approach: (6 - value) * weight
             $stressContribution = (6 - $val) * abs($weight);
             $totalScore += $stressContribution;
             $maxPossibleRaw += (5 * abs($weight));
        }

        $normalizedScore = ($totalScore / $maxPossibleRaw) * 100;

        // 2. Classify
        $label = 'Moderate';
        if ($normalizedScore <= 35) {
            $label = 'Low';
        } elseif ($normalizedScore >= 70) {
            $label = 'High';
        }

        // 3. Create Prediction Entity
        $prediction = new StressPrediction();
        $prediction->setUserWellBeingData($data);
        $prediction->setPredictedStressType($label);
        $prediction->setPredictedLabel($label); // Duplicate for legacy compatibility
        $prediction->setConfidenceScore($normalizedScore); // Using score as confidence for now
        $prediction->setModelVersion('v1.0-weighted');
        
        // 4. Generate AI Recommendation
        $this->generateRecommendation($prediction, $data);

        return $prediction;
    }

    private function generateRecommendation(StressPrediction $prediction, UserWellBeingData $data): void
    {
        $prompt = sprintf(
            "Based on the following well-being data, provide 3 brief, actionable recommendations to reduce stress. 
            Stress Level: %s (Score: %.1f/100).
            Factors: Sleep Problems: %d/5, Headaches: %d/5, Anxiety/Tension: %d/5, Heartbeat: %d/5.
            Keep it professional and empathetic. Use bullet points.",
            $prediction->getPredictedStressType(),
            $prediction->getConfidenceScore(),
            $data->getSleepProblems(),
            $data->getHeadaches(),
            $data->getAnxietyTension(),
            $data->getHeartbeatPalpitations()
        );

        $recommendation = $this->qwenService->analyzeText($prompt);
        $prediction->setRecommendation($recommendation);
    }
    public function interpretTrends(\App\Entity\User $user): string
    {
        $records = $this->wellBeingRepository->findBy(['user' => $user], ['createdAt' => 'DESC'], 10);
        
        if (count($records) < 3) {
            return "Pas assez de données pour une analyse de tendance. Continuez à enregistrer votre bien-être !";
        }

        $dataSummary = "";
        foreach ($records as $r) {
            $dataSummary .= sprintf(
                "Date: %s, Stress: %d, Sleep: %d, Confidence: %d, Anxiety: %d\n",
                $r->getCreatedAt()->format('Y-m-d'),
                $this->calculateSimpleScore($r),
                $r->getSleepProblems(),
                $r->getSubjectConfidence(),
                $r->getAnxietyTension()
            );
        }

        $prompt = "Analyze these well-being trends for the user:
        $dataSummary
        
        Identify any patterns (e.g., 'Stress spikes on certain days', 'Sleep improving/worsening').
        Also, check for 'Early Burnout signs': rising stress + low confidence + persistent sleep issues.
        
        Respond in a friendly, empathetic way. Include a warning if burnout risk is detected. 
        Always end with the medical disclaimer: 'This is not medical advice.'";

        return $this->qwenService->analyzeText($prompt);
    }

    private function calculateSimpleScore(UserWellBeingData $data): int
    {
        // Re-use logic or simplified version for trend summary
        return (int)$data->getAnxietyTension() + (int)$data->getSleepProblems() + (int)$data->getRestlessness();
    }
    public function getAggregateRiskAnalysis(): string
    {
        $allRecords = $this->wellBeingRepository->findAll();
        
        if (count($allRecords) < 5) {
            return "Pas assez de données globales pour une analyse pertinente.";
        }

        $highStressCount = 0;
        $poorSleepCount = 0;
        $lowConfidenceCount = 0;
        
        foreach ($allRecords as $r) {
            if ($this->calculateSimpleScore($r) >= 12) $highStressCount++;
            if ($r->getSleepProblems() >= 4) $poorSleepCount++;
            if ($r->getSubjectConfidence() <= 2) $lowConfidenceCount++;
        }

        $statsSummary = sprintf(
            "Total des entrées: %d. Utilisateurs en stress élevé: %d. Problèmes de sommeil majeurs: %d. Faible confiance académique: %d.",
            count($allRecords),
            $highStressCount,
            $poorSleepCount,
            $lowConfidenceCount
        );

        $prompt = "As an AI Wellness Administrator, analyze these aggregate statistics for the system:
        $statsSummary
        
        Summarize the main health risks in the population.
        Suggest 2-3 system-level recommendations (e.g., 'Organize sleep workshops', 'Academic support').
        Keep it professional and data-driven.";

        return $this->qwenService->analyzeText($prompt);
    }
}
