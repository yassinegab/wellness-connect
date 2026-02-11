<?php

namespace App\Controller\Admin;

use App\Entity\DemandeDon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/don')]
class DemandeDonAdminController extends AbstractController
{
    #[Route('/demandes', name: 'admin_demandes')]
    public function index(EntityManagerInterface $em): Response
    {
        $demandes = $em->getRepository(DemandeDon::class)
                       ->findBy([], ['dateDemande' => 'DESC']);

        return $this->render('admin/don/demandes.html.twig', [
            'demandes' => $demandes
        ]);
    }
    #[Route('/demandes/delete/{id}', name: 'admin_demande_delete', methods: ['POST'])]
public function delete(
    DemandeDon $demande,
    EntityManagerInterface $em
): Response {

    $em->remove($demande);
    $em->flush();

    $this->addFlash('success', 'Demande supprimée avec succès');

    return $this->redirectToRoute('admin_demandes');
}

}

