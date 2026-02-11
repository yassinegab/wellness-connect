<?php

namespace App\Controller\Front;

use App\Entity\Urgence;
use App\Repository\ContactUrgenceRepository;
use App\Repository\UrgenceRepository;
use App\Service\SmsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/aide/urgence')]
class UrgenceController extends AbstractController
{
    #[Route('/', name: 'urgence_index')]
    public function index(UrgenceRepository $repo): Response
    {
        return $this->render('front/urgence/index.html.twig', [
            'urgences' => $repo->findBy([], ['dateUrgence' => 'DESC']),
        ]);
    }

    #[Route('/send', name: 'urgence_send', methods: ['POST'])]
    public function send(
        Request $request,
        EntityManagerInterface $em,
        ContactUrgenceRepository $contactRepo,
        SmsService $smsService
    ): Response {
        // 1️⃣ Create urgency
        $urgence = new Urgence();
        $urgence->setMessage('Urgence déclenchée');
        $urgence->setLatitude((float) $request->request->get('latitude'));
        $urgence->setLongitude((float) $request->request->get('longitude'));
        $urgence->setStatut('EN_ATTENTE');
        $urgence->setDateUrgence(new \DateTime());

        $em->persist($urgence);
        $em->flush();

        // 2️⃣ Send SMS to emergency contacts
        $contacts = $contactRepo->findAll();

        foreach ($contacts as $contact) {
            $smsService->send(
                $contact->getTelephone(),
                "🚨 URGENCE !
📍 https://maps.google.com/?q={$urgence->getLatitude()},{$urgence->getLongitude()}
🕒 {$urgence->getDateUrgence()->format('d/m/Y H:i')}"
            );
        }

        $this->addFlash('success', '🚨 SMS envoyés avec succès');

        return $this->redirectToRoute('urgence_index');
    }

    #[Route('/delete/{id}', name: 'urgence_delete', methods: ['POST'])]
    public function delete(
        Urgence $urgence,
        EntityManagerInterface $em
    ): Response {
        $em->remove($urgence);
        $em->flush();

        $this->addFlash('success', 'Urgence supprimée');

        return $this->redirectToRoute('urgence_index');
    }
}
