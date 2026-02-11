<?php

namespace App\Controller\Admin;

use App\Entity\Donneur;
use App\Repository\DonneurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/don/donneurs')]
class DonneurAdminController extends AbstractController
{
    #[Route('', name: 'admin_donneurs')]
    public function index(DonneurRepository $repo): Response
    {
        $donneurs = $repo->findBy([], ['id' => 'DESC']);

        return $this->render('admin/don/donneurs.html.twig', [
            'donneurs' => $donneurs
        ]);
    }

    #[Route('/toggle/{id}', name: 'admin_donneur_toggle')]
    public function toggle(
        Donneur $donneur,
        EntityManagerInterface $em
    ): Response {
        $donneur->setDisponible(!$donneur->isDisponible());
        $em->flush();

        return $this->redirectToRoute('admin_donneurs');
    }

    #[Route('/delete/{id}', name: 'admin_donneur_delete')]
    public function delete(
        Donneur $donneur,
        EntityManagerInterface $em
    ): Response {
        $em->remove($donneur);
        $em->flush();

        return $this->redirectToRoute('admin_donneurs');
    }
}
