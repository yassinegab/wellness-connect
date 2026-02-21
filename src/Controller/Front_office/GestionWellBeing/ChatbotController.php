<?php

namespace App\Controller\Front_office\GestionWellBeing;

use App\Service\ChatbotService;
use App\Repository\ChatbotMessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/user/chatbot')]
class ChatbotController extends AbstractController
{
    private function getActualUser(EntityManagerInterface $em)
    {
        return $this->getUser() ?? $em->getRepository(\App\Entity\User::class)->find(1);
    }

    #[Route('', name: 'user_chatbot_index', methods: ['GET'])]
    public function index(EntityManagerInterface $em, ChatbotMessageRepository $repo): Response
    {
        $user = $this->getActualUser($em);
        $messages = $repo->findByUser($user);

        return $this->render('chatbot/index.html.twig', [
            'messages' => $messages,
        ]);
    }

    #[Route('/send', name: 'user_chatbot_send', methods: ['POST'])]
    public function send(Request $request, ChatbotService $chatbotService, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getActualUser($em);
        $content = $request->request->get('message');

        if (!$content) {
            return new JsonResponse(['error' => 'Message is empty'], 400);
        }

        // 1. Save user message
        $chatbotService->saveMessage($user, $content, 'user');

        // 2. Get AI response
        $aiResponse = $chatbotService->getResponse($user, $content);

        // 3. Save AI response
        $chatbotService->saveMessage($user, $aiResponse, 'assistant');

        return new JsonResponse([
            'message' => $aiResponse,
            'role' => 'assistant'
        ]);
    }

    #[Route('/clear', name: 'user_chatbot_clear', methods: ['POST'])]
    public function clear(EntityManagerInterface $em, ChatbotMessageRepository $repo): JsonResponse
    {
        $user = $this->getActualUser($em);
        $messages = $repo->findByUser($user);

        foreach ($messages as $msg) {
            $em->remove($msg);
        }
        $em->flush();

        return new JsonResponse(['status' => 'cleared']);
    }
}
