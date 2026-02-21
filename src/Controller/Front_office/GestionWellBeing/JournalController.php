<?php

namespace App\Controller\Front_office\GestionWellBeing;

use App\Entity\Journal;
use App\Entity\User;
use App\Repository\JournalRepository;
use App\Service\QwenService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/user/journal')]
class JournalController extends AbstractController
{
    private function getActualUser(EntityManagerInterface $em)
    {
        return $this->getUser() ?? $em->getRepository(User::class)->find(1);
    }

    #[Route('', name: 'user_journal_index', methods: ['GET', 'POST'])]
    public function index(Request $request, JournalRepository $repo, EntityManagerInterface $em, QwenService $qwenService): Response
    {
        $user = $this->getActualUser($em);
        
        if ($request->isMethod('POST')) {
            $content = $request->request->get('content');
            if ($content) {
                $journal = new Journal();
                $journal->setUser($user);
                $journal->setContent($content);
                
                // AI Emotion Detection
                $prompt = "Analyze this journal entry and identify the primary emotion (e.g., Happy, Sad, Anxious, Angry, Overwhelmed, Calm). 
                Respond with ONLY ONE WORD (the emotion).
                Entry: \"$content\"";
                
                $emotion = $qwenService->analyzeText($prompt);
                $journal->setDetectedEmotion(trim($emotion));
                
                $em->persist($journal);
                $em->flush();
                
                $this->addFlash('success', 'Journal entry saved!');
                return $this->redirectToRoute('user_journal_index');
            }
        }

        $journals = $repo->findBy(['user' => $user], ['createdAt' => 'DESC']);

        return $this->render('journal/index.html.twig', [
            'journals' => $journals,
        ]);
    }

    #[Route('/{id}/delete', name: 'user_journal_delete', methods: ['POST'])]
    public function delete(Request $request, Journal $journal, EntityManagerInterface $em): Response
    {
        $user = $this->getActualUser($em);
        if ($journal->getUser() !== $user) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('delete'.$journal->getId(), $request->request->get('_token'))) {
            $em->remove($journal);
            $em->flush();
        }

        return $this->redirectToRoute('user_journal_index');
    }
}
