<?php

namespace App\Controller\Front;

use App\Entity\Donneur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/don')]
class DonneurController extends AbstractController
{
    #[Route('/inscription', name: 'donneur_inscription')]
    public function inscription(Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {

            $nom = trim($request->request->get('nom'));
            $prenom = trim($request->request->get('prenom'));
            $age = (int) $request->request->get('age');
            $telephone = trim($request->request->get('telephone'));
            $groupe = $request->request->get('groupe_sanguin');

            $groupesAutorises = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];

            /* ================== VALIDATION ================== */

            if (strlen($nom) < 2 || strlen($prenom) < 2) {
                $this->addFlash('error', 'Nom ou prénom invalide');
                return $this->redirectToRoute('donneur_inscription');
            }

            if ($age < 18 || $age > 65) {
                $this->addFlash('error', 'Âge invalide pour le don');
                return $this->redirectToRoute('donneur_inscription');
            }

            if (!preg_match('/^[0-9]{8,15}$/', $telephone)) {
                $this->addFlash('error', 'Téléphone invalide');
                return $this->redirectToRoute('donneur_inscription');
            }

            if (!in_array($groupe, $groupesAutorises)) {
                $this->addFlash('error', 'Groupe sanguin invalide');
                return $this->redirectToRoute('donneur_inscription');
            }

            /* ================== CREATION ================== */

            $donneur = new Donneur();
            $donneur
                ->setNom($nom)
                ->setPrenom($prenom)
                ->setAge($age)
                ->setTelephone($telephone)
                ->setGroupeSanguin($groupe)
                ->setDisponible(true);

            $em->persist($donneur);
            $em->flush();

            $this->addFlash('success', '🩸 Inscription réussie. Merci pour votre engagement.');

            return $this->redirectToRoute('donneur_inscription');
        }

        return $this->render('front/don/inscription.html.twig');
    }
}
