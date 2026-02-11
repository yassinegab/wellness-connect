<?php

namespace App\Controller\Admin;

use App\Entity\Don;
use App\Repository\DonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/don')]
class DonAdminController extends AbstractController
{
    #[Route('', name: 'admin_don')]
    public function index(DonRepository $repository): Response
    {
        $dons = $repository->findBy([], ['dateDon' => 'DESC']);

        return $this->render('admin/don/index.html.twig', [
            'dons' => $dons
        ]);
    }

    #[Route('/validate/{id}', name: 'admin_don_validate')]
    public function validateDon(Don $don, EntityManagerInterface $em): Response
    {
        $don->setStatut('VALIDÉ');
        $em->flush();

        return $this->redirectToRoute('admin_don');
    }

    #[Route('/reject/{id}', name: 'admin_don_reject')]
    public function rejectDon(Don $don, EntityManagerInterface $em): Response
    {
        $don->setStatut('REFUSÉ');
        $em->flush();

        return $this->redirectToRoute('admin_don');
    }

    #[Route('/delete/{id}', name: 'admin_don_delete')]
    public function deleteDon(Don $don, EntityManagerInterface $em): Response
    {
        $em->remove($don);
        $em->flush();

        return $this->redirectToRoute('admin_don');
    }
}
