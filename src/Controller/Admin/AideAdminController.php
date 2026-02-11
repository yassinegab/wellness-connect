<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/aide')]
class AideAdminController extends AbstractController
{
    #[Route('', name: 'admin_aide')]
    public function index(): Response
    {
        return $this->render('admin/aide/index.html.twig');
    }
}
