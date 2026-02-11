<?php

namespace App\Service;

use App\Entity\Don;
use App\Entity\DemandeDon;

class DonMatchingService
{
    public function matchDemandes(DemandeDon $demande, array $dons): array
    {
        $results = [];

        foreach ($dons as $don) {

            $score = 0;

            // Urgence
            if ($demande->isUrgence()) {
                $score += 50;
            }

            // Région
            if ($demande->getRegion() === $don->getRegion()) {
                $score += 30;
            }

            // Type
            if ($demande->getTypeDemande() !== $don->getTypeDon()) {
                continue;
            }

            // Sang
            if ($demande->getTypeDemande() === 'sang') {
                if ($demande->getTypeSanguin() === $don->getTypeSanguin()) {
                    $score += 20;
                }
            }

            // Organe
            if ($demande->getTypeDemande() === 'organe') {
                if ($demande->getTypeOrgane() === $don->getTypeOrgane()) {
                    $score += 20;
                }
            }

            if ($score > 0) {
                $results[] = [
                    'don' => $don,
                    'score' => $score
                ];
            }
        }

        usort($results, fn($a, $b) => $b['score'] <=> $a['score']);

        return $results;
    }
}
