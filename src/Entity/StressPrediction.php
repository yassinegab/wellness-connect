<?php

namespace App\Entity;

use App\Repository\StressPredictionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StressPredictionRepository::class)]
class StressPrediction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $predictedStressType = null;

    #[ORM\Column(length: 255)]
    private ?string $predictedLabel = null;

    // FIX: float instead of string
    #[ORM\Column(type: 'float')]
    private ?float $confidenceScore = null;

    #[ORM\Column(length: 50)]
    private ?string $modelVersion = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $recommendation = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'stressPredictions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?UserWellBeingData $userWellBeingData = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPredictedStressType(): ?string
    {
        return $this->predictedStressType;
    }

    public function setPredictedStressType(string $predictedStressType): static
    {
        $this->predictedStressType = $predictedStressType;
        return $this;
    }

    public function getPredictedLabel(): ?string
    {
        return $this->predictedLabel;
    }

    public function setPredictedLabel(string $predictedLabel): static
    {
        $this->predictedLabel = $predictedLabel;
        return $this;
    }

    public function getConfidenceScore(): ?float
    {
        return $this->confidenceScore;
    }

    public function setConfidenceScore(float $confidenceScore): static
    {
        $this->confidenceScore = $confidenceScore;
        return $this;
    }

    public function getModelVersion(): ?string
    {
        return $this->modelVersion;
    }

    public function setModelVersion(string $modelVersion): static
    {
        $this->modelVersion = $modelVersion;
        return $this;
    }

    public function getRecommendation(): ?string
    {
        return $this->recommendation;
    }

    public function setRecommendation(?string $recommendation): static
    {
        $this->recommendation = $recommendation;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUserWellBeingData(): ?UserWellBeingData
    {
        return $this->userWellBeingData;
    }

    public function setUserWellBeingData(?UserWellBeingData $userWellBeingData): static
    {
        $this->userWellBeingData = $userWellBeingData;
        return $this;
    }
}
