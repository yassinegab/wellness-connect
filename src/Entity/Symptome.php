<?php

namespace App\Entity;

use App\Repository\SymptomeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SymptomeRepository::class)]
class Symptome
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $IdSymptome = null;

   

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(length: 255)]
    private ?string $intensite = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $DateObservation = null;


    public function getIdSymptome(): ?int
    {
        return $this->IdSymptome;
    }


    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getIntensite(): ?string
    {
        return $this->intensite;
    }

    public function setIntensite(string $intensite): static
    {
        $this->intensite = $intensite;

        return $this;
    }

    public function getDateObservation(): ?\DateTime
    {
        return $this->DateObservation;
    }

    public function setDateObservation(?\DateTime $DateObservation): static
    {
        $this->DateObservation = $DateObservation;

        return $this;
    }
}
