<?php

namespace App\Entity\Front_office;

use App\Entity\User;
use App\Repository\DossierMedicalRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DossierMedicalRepository::class)]
class DossierMedical
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $antecedentsMedicaux = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $maladiesChroniques = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $allergies = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $traitementsEnCours = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $diagnostics = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notesMedecin = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $derniereMiseAJour = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $objectifSante = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $niveauActivite = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'dossiersMedicaux')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    // ================= GETTERS & SETTERS =================

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAntecedentsMedicaux(): ?string
    {
        return $this->antecedentsMedicaux;
    }

    public function setAntecedentsMedicaux(?string $antecedentsMedicaux): self
    {
        $this->antecedentsMedicaux = $antecedentsMedicaux;

        return $this;
    }

    public function getMaladiesChroniques(): ?string
    {
        return $this->maladiesChroniques;
    }

    public function setMaladiesChroniques(?string $maladiesChroniques): self
    {
        $this->maladiesChroniques = $maladiesChroniques;

        return $this;
    }

    public function getAllergies(): ?string
    {
        return $this->allergies;
    }

    public function setAllergies(?string $allergies): self
    {
        $this->allergies = $allergies;

        return $this;
    }

    public function getTraitementsEnCours(): ?string
    {
        return $this->traitementsEnCours;
    }

    public function setTraitementsEnCours(?string $traitementsEnCours): self
    {
        $this->traitementsEnCours = $traitementsEnCours;

        return $this;
    }

    public function getDiagnostics(): ?string
    {
        return $this->diagnostics;
    }

    public function setDiagnostics(?string $diagnostics): self
    {
        $this->diagnostics = $diagnostics;

        return $this;
    }

    public function getNotesMedecin(): ?string
    {
        return $this->notesMedecin;
    }

    public function setNotesMedecin(?string $notesMedecin): self
    {
        $this->notesMedecin = $notesMedecin;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): self
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getDerniereMiseAJour(): ?\DateTimeInterface
    {
        return $this->derniereMiseAJour;
    }

    public function setDerniereMiseAJour(\DateTimeInterface $derniereMiseAJour): self
    {
        $this->derniereMiseAJour = $derniereMiseAJour;

        return $this;
    }

    public function getObjectifSante(): ?string
    {
        return $this->objectifSante;
    }

    public function setObjectifSante(?string $objectifSante): self
    {
        $this->objectifSante = $objectifSante;

        return $this;
    }

    public function getNiveauActivite(): ?string
    {
        return $this->niveauActivite;
    }

    public function setNiveauActivite(?string $niveauActivite): self
    {
        $this->niveauActivite = $niveauActivite;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
    public function __construct()
{
    $this->dateCreation = new \DateTime();
    $this->derniereMiseAJour = new \DateTime();
}
}
