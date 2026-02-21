<?php

namespace App\Entity;

use App\Repository\CycleRepository;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CycleRepository::class)]
class Cycle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $idCycle = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: "La date de début est obligatoire")]
    private ?\DateTime $dateDebutM = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: "La date de fin est obligatoire")]
    private ?\DateTime $dateFinM = null;

    #[Assert\Callback]
    public function validateDates(ExecutionContextInterface $context): void
    {
        if ($this->dateDebutM && $this->dateFinM) {
            if ($this->dateDebutM >= $this->dateFinM) {
                $context->buildViolation(
                    'La date de début doit être strictement antérieure à la date de fin'
                )
                ->atPath('dateDebutM')
                ->addViolation();
            }
        }
    }

  
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
