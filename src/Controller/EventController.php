<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/dashboard')]
class EventController extends AbstractController
{
    #[Route('/event', name: 'admin_events_index')]
    public function index(EventRepository $eventRepository): Response
    {
        $events = $eventRepository->findBy([], ['date' => 'ASC']); // tous les événements triés par date
        return $this->render('admin/dashboard/event.html.twig', [
            'events' => $events
        ]);
    }

 
    #[Route('/add', name: 'admin_events_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($event);
            $em->flush();

            $this->addFlash('success', 'Événement ajouté avec succès !');

            return $this->redirectToRoute('admin_events_index');
        }

        return $this->render('admin/dashboard/add.html.twig', [
            'form' => $form->createView()
        ]);
    }


    #[Route('/edit/{id}', name: 'admin_events_edit')]
public function edit(Event $event, Request $request, EntityManagerInterface $em): Response
{
    // Création du formulaire avec l'entité existante
    $form = $this->createForm(EventType::class, $event);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em->flush(); // l'entité est déjà en base, pas besoin de persist()

        $this->addFlash('success', 'Événement modifié avec succès !');

        return $this->redirectToRoute('admin_events_index');
    }

    return $this->render('admin/dashboard/edit.html.twig', [
        'form' => $form->createView(),
        'event' => $event
    ]);
}

    #[Route('/delete/{id}', name: 'admin_events_delete')]
    public function delete(Event $event, EntityManagerInterface $em): Response
    {
        $em->remove($event);
        $em->flush();

        $this->addFlash('success', 'Événement supprimé !');
        return $this->redirectToRoute('admin_events_index');
    }
}