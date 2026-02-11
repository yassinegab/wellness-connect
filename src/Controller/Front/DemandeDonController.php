<?php

namespace App\Controller\Front;

use App\Entity\DemandeDon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/don')]
class DemandeDonController extends AbstractController
{
    #[Route('/demande', name: 'demande_don')]
    public function demande(Request $request, EntityManagerInterface $em): Response
    {
        /* ➕ AJOUT */
        if ($request->isMethod('POST')) {

            $typeDemande = $request->request->get('type_demande');
            $typeOrgane  = $request->request->get('type_organe');
            $typeSanguin = $request->request->get('type_sanguin');
            $region      = trim($request->request->get('region'));
            $urgence     = $request->request->get('urgence') === '1';

            if (!in_array($typeDemande, ['sang', 'organe'])) {
                $this->addFlash('error', 'Type invalide');
                return $this->redirectToRoute('demande_don');
            }

            if ($typeDemande === 'organe' && empty($typeOrgane)) {
                $this->addFlash('error', 'Organe requis');
                return $this->redirectToRoute('demande_don');
            }

            if ($typeDemande === 'sang' && empty($typeSanguin)) {
                $this->addFlash('error', 'Groupe requis');
                return $this->redirectToRoute('demande_don');
            }

            if (strlen($region) < 3) {
                $this->addFlash('error', 'Région invalide');
                return $this->redirectToRoute('demande_don');
            }

            $demande = new DemandeDon();
            $demande->setTypeDemande($typeDemande);
            $demande->setRegion($region);
            $demande->setUrgence($urgence);
            $demande->setRangAttente(1);
            $demande->setDateDemande(new \DateTime());

            if ($typeDemande === 'organe') {
                $demande->setTypeOrgane($typeOrgane);
                $demande->setTypeSanguin(null);
            } else {
                $demande->setTypeSanguin($typeSanguin);
                $demande->setTypeOrgane(null);
            }

            $em->persist($demande);
            $em->flush();

            $this->addFlash('success', 'Demande ajoutée');
            return $this->redirectToRoute('demande_don');
        }

        /* 📋 LISTE */
        $demandes = $em->getRepository(DemandeDon::class)
            ->findBy([], ['dateDemande' => 'DESC']);

        return $this->render('front/don/demande.html.twig', [
            'demandes' => $demandes
        ]);
    }

    #[Route('/demande/delete/{id}', name: 'demande_delete', methods: ['POST'])]
    public function delete(DemandeDon $demande, EntityManagerInterface $em): Response
    {
        $em->remove($demande);
        $em->flush();

        $this->addFlash('success', 'Demande supprimée');
        return $this->redirectToRoute('demande_don');
    }

    #[Route('/demande/edit/{id}', name: 'demande_edit', methods: ['POST'])]
    public function edit(DemandeDon $demande, Request $request, EntityManagerInterface $em): Response
    {
        $demande->setRegion(trim($request->request->get('region')));
        $demande->setUrgence($request->request->get('urgence') === '1');

        $em->flush();

        $this->addFlash('success', 'Demande modifiée');
        return $this->redirectToRoute('demande_don');
    }
}
