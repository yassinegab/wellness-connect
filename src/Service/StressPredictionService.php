<?php

namespace App\Service;

use App\Entity\StressPrediction;
use App\Entity\UserWellBeingData;
use Doctrine\ORM\EntityManagerInterface;

class StressPredictionService
{
    private QwenService $qwenService;
    private EntityManagerInterface $entityManager;

    public function __construct(QwenService $qwenService, EntityManagerInterface $entityManager)
    {
        $this->qwenService = $qwenService;
        $this->entityManager = $entityManager;
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
}
