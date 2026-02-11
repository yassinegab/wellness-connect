<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class DemandeDon
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private string $typeDemande;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $typeOrgane = null;

    #[ORM\Column(length: 5, nullable: true)]
    private ?string $typeSanguin = null;

    #[ORM\Column(length: 50)]
    private string $region;

    #[ORM\Column(type: 'boolean')]
    private bool $urgence = false;

    #[ORM\Column]
    private int $rangAttente = 1;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $dateDemande;

    /* ================= GETTERS / SETTERS ================= */

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTypeDemande(): string
    {
        return $this->typeDemande;
    }

    public function setTypeDemande(string $typeDemande): self
    {
        $this->typeDemande = $typeDemande;
        return $this;
    }

    public function getTypeOrgane(): ?string
    {
        return $this->typeOrgane;
    }

    public function setTypeOrgane(?string $typeOrgane): self
    {
        $this->typeOrgane = $typeOrgane;
        return $this;
    }

    public function getTypeSanguin(): ?string
    {
        return $this->typeSanguin;
    }

    public function setTypeSanguin(?string $typeSanguin): self
    {
        $this->typeSanguin = $typeSanguin;
        return $this;
    }

    public function getRegion(): string
    {
        return $this->region;
    }

    public function setRegion(string $region): self
    {
        $this->region = $region;
        return $this;
    }

    public function isUrgence(): bool
    {
        return $this->urgence;
    }

    public function setUrgence(bool $urgence): self
    {
        $this->urgence = $urgence;
        return $this;
    }

    public function getRangAttente(): int
    {
        return $this->rangAttente;
    }

    public function setRangAttente(int $rangAttente): self
    {
        $this->rangAttente = $rangAttente;
        return $this;
    }

    public function getDateDemande(): \DateTimeInterface
    {
        return $this->dateDemande;
    }

    public function setDateDemande(\DateTimeInterface $dateDemande): self
    {
        $this->dateDemande = $dateDemande;
        return $this;
    }
}
