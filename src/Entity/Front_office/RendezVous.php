<?php

namespace App\Entity\Front_office;

use App\Repository\RendezVousRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RendezVousRepository::class)]
#[ORM\HasLifecycleCallbacks]
class RendezVous
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $patient = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $medecin = null;

    // ✅ RELATION ManyToOne : Un rendez-vous appartient à un hôpital
    #[ORM\ManyToOne(targetEntity: Hopital::class, inversedBy: 'rendezVous')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "L'hôpital est obligatoire")]
    private ?Hopital $hopital = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le type de consultation est obligatoire")]
    #[Assert\Choice(
        choices: ['Présentiel', 'Téléconsultation', 'Urgence'],
        message: "Type de consultation invalide"
    )]
    private ?string $typeConsultation = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le statut est obligatoire")]
    #[Assert\Choice(
        choices: ['En attente', 'Confirmé', 'Terminé', 'Annulé'],
        message: "Statut invalide"
    )]
    private ?string $statut = 'En attente';

    #[ORM\Column(nullable: true)]
    #[Assert\Range(
        min: 0,
        max: 100,
        notInRangeMessage: "Le score AI doit être entre {{ min }} et {{ max }}"
    )]
    private ?float $scoreAI = null;

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotBlank(message: "La date du rendez-vous est obligatoire")]
    #[Assert\GreaterThanOrEqual(
        value: 'today',
        message: "La date du rendez-vous ne peut pas être dans le passé"
    )]
    private ?\DateTimeInterface $dateRendezVous = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\Length(
        max: 1000,
        maxMessage: "Les notes ne peuvent pas dépasser {{ limit }} caractères"
    )]
    private ?string $notes = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->statut = 'En attente';
    }

    #[ORM\PreUpdate]
    public function preUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getMedecin(): ?User
    {
        return $this->medecin;
    }

    public function setMedecin(?User $medecin): static
    {
        $this->medecin = $medecin;
        return $this;
    }

    public function getHopital(): ?Hopital
    {
        return $this->hopital;
    }

    public function setHopital(?Hopital $hopital): static
    {
        $this->hopital = $hopital;
        return $this;
    }

    public function getTypeConsultation(): ?string
    {
        return $this->typeConsultation;
    }

    public function setTypeConsultation(?string $typeConsultation): static
    {
        $this->typeConsultation = $typeConsultation;
        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(?string $statut): static
    {
        $this->statut = $statut;
        return $this;
    }

    public function getScoreAI(): ?float
    {
        return $this->scoreAI;
    }

    public function setScoreAI(?float $scoreAI): static
    {
        $this->scoreAI = $scoreAI;
        return $this;
    }

    public function getDateRendezVous(): ?\DateTimeInterface
    {
        return $this->dateRendezVous;
    }

    public function setDateRendezVous(?\DateTimeInterface $dateRendezVous): static
    {
        $this->dateRendezVous = $dateRendezVous;
        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): static
    {
        $this->notes = $notes;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt instanceof \DateTime
            ? \DateTimeImmutable::createFromMutable($updatedAt)
            : $updatedAt;

        return $this;
    }

    public function __toString(): string
    {
        return sprintf('Rendez-vous #%d (%s)', $this->id ?? 0, $this->dateRendezVous?->format('d/m/Y H:i') ?? 'Non défini');
    }
}