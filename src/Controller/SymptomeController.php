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


class SymptomeController extends AbstractController
{
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




  
}