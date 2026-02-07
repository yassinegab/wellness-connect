<?php

namespace App\Entity;

use App\Repository\AnalyseAIRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AnalyseAIRepository::class)]
class AnalyseAI
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank(message: "Les symptômes sont obligatoires")]
    #[Assert\Length(
        min: 10,
        max: 2000,
        minMessage: "Les symptômes doivent contenir au moins {{ limit }} caractères",
        maxMessage: "Les symptômes ne peuvent pas dépasser {{ limit }} caractères"
    )]
    private ?string $symptomes = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Le niveau de risque est obligatoire")]
    #[Assert\Range(
        min: 0,
        max: 100,
        notInRangeMessage: "Le niveau de risque doit être entre {{ min }} et {{ max }}"
    )]
    private ?float $niveauRisque = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La spécialité recommandée est obligatoire")]
    #[Assert\Choice(
        choices: ['Médecine générale', 'Cardiologie', 'Dermatologie', 'Pédiatrie', 'Gynécologie', 'Urgences', 'Autre'],
        message: "Spécialité invalide"
    )]
    private ?string $specialiteRecommandee = null;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank(message: "La décision proposée est obligatoire")]
    #[Assert\Length(
        min: 10,
        max: 1000,
        minMessage: "La décision doit contenir au moins {{ limit }} caractères",
        maxMessage: "La décision ne peut pas dépasser {{ limit }} caractères"
    )]
    private ?string $decisionProposee = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "Le patient est obligatoire")]
    private ?User $patient = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSymptomes(): ?string
    {
        return $this->symptomes;
    }

    public function setSymptomes(string $symptomes): static
    {
        $this->symptomes = $symptomes;
        return $this;
    }

    public function getNiveauRisque(): ?float
    {
        return $this->niveauRisque;
    }

    public function setNiveauRisque(float $niveauRisque): static
    {
        $this->niveauRisque = $niveauRisque;
        return $this;
    }

    public function getSpecialiteRecommandee(): ?string
    {
        return $this->specialiteRecommandee;
    }

    public function setSpecialiteRecommandee(string $specialiteRecommandee): static
    {
        $this->specialiteRecommandee = $specialiteRecommandee;
        return $this;
    }

    public function getDecisionProposee(): ?string
    {
        return $this->decisionProposee;
    }

    public function setDecisionProposee(string $decisionProposee): static
    {
        $this->decisionProposee = $decisionProposee;
        return $this;
    }

    public function getPatient(): ?User
    {
        return $this->patient;
    }

    public function setPatient(?User $patient): static
    {
        $this->patient = $patient;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getRisqueLabel(): string
    {
        if ($this->niveauRisque >= 80) {
            return 'Très élevé';
        } elseif ($this->niveauRisque >= 60) {
            return 'Élevé';
        } elseif ($this->niveauRisque >= 40) {
            return 'Modéré';
        } else {
            return 'Faible';
        }
    }

    public function getRisqueColor(): string
    {
        if ($this->niveauRisque >= 80) {
            return '#FF3B30';
        } elseif ($this->niveauRisque >= 60) {
            return '#FF9500';
        } elseif ($this->niveauRisque >= 40) {
            return '#FFCC00';
        } else {
            return '#34C759';
        }
    }
}