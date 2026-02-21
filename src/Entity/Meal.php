<?php

namespace App\Entity;

use App\Repository\MealRepository;
use App\Entity\User;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MealRepository::class)]
class Meal
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Image name (file required)
    #[ORM\Column(length: 255)]
    // #[Assert\NotBlank(message: "Please upload a meal image.")]
    #[Assert\Length(
        max: 255,
        maxMessage: "Image name is too long."
    )]
    private ?string $imageName = null;

    // Meal description
    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "Description is required.")]
    #[Assert\Length(
        min: 5,
        minMessage: "Description must be at least {{ limit }} characters.",
        max: 1000,
        maxMessage: "Description cannot exceed {{ limit }} characters."
    )]
    private ?string $description = null;

    // AI analysis (optional)
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Length(
        max: 2000,
        maxMessage: "AI analysis is too long."
    )]
    private ?string $aiAnalysis = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createAt = null;

    #[ORM\Column(nullable: true)]
    private ?float $calories = null;

    #[ORM\Column(nullable: true)]
    private ?float $sugar = null;

    #[ORM\Column(nullable: true)]
    private ?float $protein = null;

    public function __construct()
    {
        $this->createAt = new \DateTimeImmutable(); // automatically set now
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(string $imageName): static
    {
        $this->imageName = $imageName;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getAiAnalysis(): ?string
    {
        return $this->aiAnalysis;
    }

    public function setAiAnalysis(?string $aiAnalysis): static
    {
        $this->aiAnalysis = $aiAnalysis;
        return $this;
    }

    public function getCreateAt(): ?\DateTimeImmutable
    {
        return $this->createAt;
    }

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function setCreateAt(\DateTimeImmutable $createAt): static
    {
        $this->createAt = $createAt;
        return $this;
    }

    public function getCalories(): ?float
    {
        return $this->calories;
    }

    public function setCalories(?float $calories): static
    {
        $this->calories = $calories;
        return $this;
    }

    public function getSugar(): ?float
    {
        return $this->sugar;
    }

    public function setSugar(?float $sugar): static
    {
        $this->sugar = $sugar;
        return $this;
    }

    public function getProtein(): ?float
    {
        return $this->protein;
    }

    public function setProtein(?float $protein): static
    {
        $this->protein = $protein;
        return $this;
    }
}
