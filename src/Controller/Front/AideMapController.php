<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/aide')]
class AideMapController extends AbstractController
{
    #[Route('/map', name: 'aide_map')]
    public function index(): Response
    {
        return $this->render('front/aide/map.html.twig');
    }
}