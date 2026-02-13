<?php

namespace App\Controller\Front_office;
use App\Entity\DossierMedical;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/medecin/dossier')]
#[IsGranted('ROLE_MEDECIN')]
final class MedecinController extends AbstractController
{
    #[Route('/{id}', name: 'medecin_dossier_consulter')]
    public function consulter(DossierMedical $dossier): Response
    {
        return $this->render('medecin/dossier/consulter.html.twig', [
            'dossier' => $dossier,
        ]);
    }
}
