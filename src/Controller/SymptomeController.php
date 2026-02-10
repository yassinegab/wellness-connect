<?php

namespace App\Controller;

use App\Entity\Symptome;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class SymptomeController extends AbstractController
{
    #[Route('/symptomes', name: 'symptome_index')]
    public function index(): Response
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

        return $this->render('symptome/index.html.twig', [
            'symptomesDisponibles' => $symptomesDisponibles,
            'intensites' => $intensites
        ]);
    }

    #[Route('/symptomes/create', name: 'symptome_create', methods: ['POST'])]
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
}