<?php

namespace App\Controller\Front_office;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/medecin')]
#[IsGranted('ROLE_MEDECIN')]
class MedecinController extends AbstractController
{
    /**
     * Dashboard principal du médecin
     */
    #[Route('/dashboard', name: 'medecin_dashboard')]
    public function dashboard(): Response
    {
        return $this->render('medecin/index.html.twig', [
            'user' => $this->getUser(),
        ]);
    }
   
    /**
     * Liste de tous les dossiers médicaux
     */
    #[Route('/dossiers', name: 'medecin_dossiers_liste')]
    public function listeDossiers(): Response
    {
        // TODO: Récupérer les dossiers depuis la base de données
        return $this->render('medecin/dossiers_liste.html.twig', [
            'user' => $this->getUser(),
        ]);
    }
    
    /**
     * Consulter un dossier médical spécifique
     */
    #[Route('/dossiers/{id}', name: 'medecin_dossier_detail', requirements: ['id' => '\d+'])]
    public function consulterDossier(int $id): Response
    {
        // TODO: Récupérer le dossier depuis la base de données
        return $this->render('medecin/dossier_detail.html.twig', [
            'user' => $this->getUser(),
            'dossierId' => $id,
        ]);
    }
}