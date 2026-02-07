<?php

namespace App\Entity;

use App\Repository\CycleRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CycleRepository::class)]
class Cycle
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $idCycle = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $dateDebutM = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $dateFinM = null;



    public function getIdCycle(): ?int
    {
        return $this->idCycle;
    }



    public function getDateDebutM(): ?\DateTime
    {
        return $this->dateDebutM;
    }

    public function setDateDebutM(\DateTime $dateDebutM): static
    {
        $this->dateDebutM = $dateDebutM;

        return $this;
    }

    public function getDateFinM(): ?\DateTime
    {
        return $this->dateFinM;
    }

    public function setDateFinM(\DateTime $dateFinM): static
    {
        $this->dateFinM = $dateFinM;

        return $this;
    }
}
