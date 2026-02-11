<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SupportController extends AbstractController
{
    #[Route('/don', name: 'don')]
    public function don(): Response
    {
        return $this->render('don/index.html.twig', [
            'activeModule' => 'don',
        ]);
    }

    #[Route('/aide', name: 'aide')]
    public function aide(): Response
    {
        return $this->render('aide/index.html.twig', [
            'activeModule' => 'aide',
        ]);
    }
}
