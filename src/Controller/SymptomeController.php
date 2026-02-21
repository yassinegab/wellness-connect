<?php

namespace App\Controller;

use App\Entity\Symptome;
use App\Form\SymptomeType;
use App\Repository\SymptomeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
class SymptomeController extends AbstractController
{

private $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    #[Route('/symptome', name: 'symptome_index')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $symptomesDisponibles = [
            ['label' => 'Maux de tête', 'value' => 'maux_tete'],
            ['label' => 'Crampes', 'value' => 'crampes'],
            ['label' => 'Fatigue', 'value' => 'fatigue'],
            ['label' => 'Ballonnements', 'value' => 'ballonnements'],
        ];

        $intensites = [
            'Très légère 🌱' => 1,
            'Légère 🙂'      => 2,
            'Modérée 😐'     => 3,
            'Forte 😣'       => 4,
            'Très forte 😖'  => 5,
        ];

        $symptomes = $doctrine->getRepository(Symptome::class)->findAll();

        return $this->render('symptome/index.html.twig', [
            'symptomesDisponibles' => $symptomesDisponibles,
            'intensites' => $intensites,
            'symptomes' => $symptomes,
        ]);
    }


    #[Route('/symptome/create', name: 'symptome_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        foreach ($data as $s) {
            $symptome = new Symptome();
            $symptome->setType($s['type']);
            $symptome->setIntensite($s['intensite']);
            $symptome->setDateObservation(new \DateTime($s['dateObservation']));

            $em->persist($symptome);
        }

        $em->flush();

        return new JsonResponse(['status' => 'success']);
    }
    #[Route('/symptome/{id}/edit', name: 'symptome_edit')]
    public function edit(Request $request, Symptome $symptome, EntityManagerInterface $em): Response
   {
    // Création du formulaire lié à l'entité Symptome
    $form = $this->createForm(SymptomeType::class, $symptome);

    // Traitement de la requête
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em->persist($symptome);
        $em->flush();

        $this->addFlash('success', 'Symptôme modifié avec succès !');
        return $this->redirectToRoute('symptome_index');
    }

    return $this->render('symptome/edit.html.twig', [
        'form' => $form->createView(),
        'symptome' => $symptome,
    ]);
   } 
  #[Route('/symptome/list', name: 'symptome_list')]
  public function list(SymptomeRepository $symptomeRepository): Response
 {
    $symptomes = $symptomeRepository->findAll();

    return $this->render('symptome/list.html.twig', [
        'symptomes' => $symptomes,
    ]);
 }
  
#[Route('/ai/chat', name: 'ai_chat', methods: ['POST'])]
public function chat(Request $request, HttpClientInterface $httpClient): JsonResponse
{
    $data = json_decode($request->getContent(), true);
    $userMessage = $data['message'] ?? '';

    if (empty($userMessage)) {
        return new JsonResponse(['reply' => 'Le message est vide. 🌸']);
    }

    $apiKey = 'AIzaSyAY2ZQiC34DAdXvskuy8V-wkOSuhY3gb7I'; 

    try {
        // FREE TIER BEST SETTINGS: v1beta + gemini-1.5-flash
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=' . $apiKey;

        $response = $httpClient->request('POST', $url, [
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'json' => [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $userMessage]
                        ]
                    ]
                ]
            ],
            'verify_peer' => false,
            'verify_host' => false,
        ]);

        $result = $response->toArray(false);
        
        // Check for success
        if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
            $aiText = $result['candidates'][0]['content']['parts'][0]['text'];
            return new JsonResponse(['reply' => $aiText]);
        }

        // Handle specific Free Tier errors (like Rate Limiting)
        if (isset($result['error'])) {
            return new JsonResponse([
                'reply' => "Désolé, le service gratuit est saturé ou indisponible : " . ($result['error']['message'] ?? 'Erreur inconnue')
            ], 200);
        }

        return new JsonResponse(['reply' => 'L’IA n’a pas pu répondre. Réessayez dans un instant.']);

    } catch (\Exception $e) {
        return new JsonResponse(['reply' => "Erreur : " . $e->getMessage()], 200); 
    }
}
}