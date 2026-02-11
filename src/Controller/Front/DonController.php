<?php

namespace App\Controller\Front;

use App\Entity\Don;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/don')]
class DonController extends AbstractController
{
    #[Route('/new', name: 'don_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        // 🔎 FILTRES
        $sort = $request->query->get('sort', 'date_desc');
        $date = $request->query->get('date');

        // ➕ AJOUT DON
        if ($request->isMethod('POST')) {
            $don = new Don();
            $don->setTypeDon($request->request->get('type_don'));
            $don->setTypeSanguin($request->request->get('type_sanguin'));
            $don->setTypeOrgane($request->request->get('type_organe'));
            $don->setRegion($request->request->get('region'));
            $don->setUrgence($request->request->get('urgence') === '1');
            $don->setStatut('EN_ATTENTE');
            $don->setDateDon(new \DateTime());

            $em->persist($don);
            $em->flush();

            $this->addFlash('success', '🩸 Don déclaré avec succès');

            return $this->redirectToRoute('don_new');
        }

        // 📋 LISTE + TRI + FILTRE
        $qb = $em->getRepository(Don::class)->createQueryBuilder('d');

        if ($date) {
            $start = new \DateTime($date . ' 00:00:00');
            $end   = new \DateTime($date . ' 23:59:59');

            $qb->andWhere('d.dateDon BETWEEN :start AND :end')
               ->setParameter('start', $start)
               ->setParameter('end', $end);
        }

        if ($sort === 'date_asc') {
            $qb->orderBy('d.dateDon', 'ASC');
        } else {
            $qb->orderBy('d.dateDon', 'DESC');
        }

        $dons = $qb->getQuery()->getResult();

        return $this->render('front/don/new.html.twig', [
            'dons' => $dons,
            'sort' => $sort,
            'date' => $date
        ]);
    }

    #[Route('/delete/{id}', name: 'don_delete', methods: ['POST'])]
    public function delete(Don $don, EntityManagerInterface $em): Response
    {
        $em->remove($don);
        $em->flush();

        $this->addFlash('success', '🗑 Don supprimé');

        return $this->redirectToRoute('don_new');
    }
}
