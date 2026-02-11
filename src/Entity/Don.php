<?php

namespace App\Entity;
use App\Repository\DonRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DonRepository::class)]
class Don

{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name: 'type_don', length: 20)]
    private string $typeDon;

    #[ORM\Column(name: 'type_sanguin', length: 10, nullable: true)]
    private ?string $typeSanguin = null;

    #[ORM\Column(name: 'type_organe', length: 50, nullable: true)]
    private ?string $typeOrgane = null;

    #[ORM\Column(length: 50)]
    private string $region;

    #[ORM\Column]
    private bool $urgence = false;

    #[ORM\Column(length: 20)]
    private string $statut = 'EN_ATTENTE';

    #[ORM\Column(name: 'date_don', type: 'datetime')]
    private \DateTime $dateDon;

    public function __construct()
    {
        $this->dateDon = new \DateTime();
    }

    // ===== GETTERS & SETTERS =====

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTypeDon(): string
    {
        return $this->typeDon;
    }

    public function setTypeDon(string $typeDon): self
    {
        $this->typeDon = $typeDon;
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

    public function getTypeOrgane(): ?string
    {
        return $this->typeOrgane;
    }

    public function setTypeOrgane(?string $typeOrgane): self
    {
        $this->typeOrgane = $typeOrgane;
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

    public function getStatut(): string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;
        return $this;
    }

    public function getDateDon(): \DateTime
    {
        return $this->dateDon;
    }

    public function setDateDon(\DateTime $dateDon): self
    {
        $this->dateDon = $dateDon;
        return $this;
    }
}
