<?php

namespace App\Controller\Front_office;
use App\Entity\Front_office\Hopital;

use App\Repository\HopitalRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/hopitaux')]
class HopitalController extends AbstractController
{
#[Route('/hopitaux', name: 'frontoffice_hopitaux')]   
    public function index(HopitalRepository $hopitalRepository): Response
    {
        return $this->render('hopital/index.html.twig', [
            'hopitaux' => $hopitalRepository->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'app_hopital_show', methods: ['GET'])]
    public function show(Hopital $hopital): Response
    {
        return $this->render('hopital/show.html.twig', [
            'hopital' => $hopital,
        ]);
    }
}