<?php

namespace App\Entity;

use App\Repository\UserWellBeingDataRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserWellBeingDataRepository::class)]
class UserWellBeingData
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: "integer")]
    #[Assert\NotBlank(message: "Sleep problems score is required.")]
    #[Assert\Range(min: 1, max: 5)]
    private ?int $workEnvironment = null;

    #[ORM\Column(type: "integer")]
    #[Assert\NotBlank(message: "Sleep problems score is required.")]
    #[Assert\Range(min: 1, max: 5)]
    private ?int $sleepProblems = null;

    #[ORM\Column(type: "integer")]
    #[Assert\NotBlank(message: "Headaches score is required.")]
    #[Assert\Range(min: 1, max: 5)]
    private ?int $headaches = null;

    #[ORM\Column(type: "integer")]
    #[Assert\NotBlank(message: "Restlessness score is required.")]
    #[Assert\Range(min: 1, max: 5)]
    private ?int $restlessness = null;

    #[ORM\Column(type: "integer")]
    #[Assert\NotBlank(message: "Heartbeat palpitations score is required.")]
    #[Assert\Range(min: 1, max: 5)]
    private ?int $heartbeatPalpitations = null;

    #[ORM\Column(type: "integer")]
    #[Assert\NotBlank(message: "Low academic confidence score is required.")]
    #[Assert\Range(min: 1, max: 5)]
    private ?int $lowAcademicConfidence = null;

    #[ORM\Column(type: "integer")]
    #[Assert\NotBlank(message: "Class attendance score is required.")]
    #[Assert\Range(min: 1, max: 5)]
    private ?int $classAttendance = null;

    #[ORM\Column(type: "integer")]
    #[Assert\NotBlank(message: "Anxiety tension score is required.")]
    #[Assert\Range(min: 1, max: 5)]
    private ?int $anxietyTension = null;

    #[ORM\Column(type: "integer")]
    #[Assert\NotBlank(message: "Irritability score is required.")]
    #[Assert\Range(min: 1, max: 5)]
    private ?int $irritability = null;

    #[ORM\Column(type: "integer")]
    #[Assert\NotBlank(message: "Subject confidence score is required.")]
    #[Assert\Range(min: 1, max: 5)]
    private ?int $subjectConfidence = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    /**
     * @var Collection<int, StressPrediction>
     */
    #[ORM\OneToMany(targetEntity: StressPrediction::class, mappedBy: 'userWellBeingData', cascade: ['persist', 'remove'])]
    private Collection $stressPredictions;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->stressPredictions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return Collection<int, StressPrediction>
     */
    public function getStressPredictions(): Collection
    {
        return $this->stressPredictions;
    }

    public function addStressPrediction(StressPrediction $prediction): static
    {
        if (!$this->stressPredictions->contains($prediction)) {
            $this->stressPredictions->add($prediction);
            $prediction->setUserWellBeingData($this);
        }

        return $this;
    }

    public function removeStressPrediction(StressPrediction $prediction): static
    {
        if ($this->stressPredictions->removeElement($prediction)) {
            if ($prediction->getUserWellBeingData() === $this) {
                $prediction->setUserWellBeingData(null);
            }
        }

        return $this;
    }

    // ---- Getters & Setters for scores ----
    public function getWorkEnvironment(): ?int
    {
        return $this->workEnvironment;
    }
    public function setWorkEnvironment(?int $v): static
    {
        $this->workEnvironment = $v;
        return $this;
    }

    public function getSleepProblems(): ?int
    {
        return $this->sleepProblems;
    }
    public function setSleepProblems(?int $v): static
    {
        $this->sleepProblems = $v;
        return $this;
    }

    public function getHeadaches(): ?int
    {
        return $this->headaches;
    }
    public function setHeadaches(?int $v): static
    {
        $this->headaches = $v;
        return $this;
    }

    public function getRestlessness(): ?int
    {
        return $this->restlessness;
    }
    public function setRestlessness(?int $v): static
    {
        $this->restlessness = $v;
        return $this;
    }

    public function getHeartbeatPalpitations(): ?int
    {
        return $this->heartbeatPalpitations;
    }
    public function setHeartbeatPalpitations(?int $v): static
    {
        $this->heartbeatPalpitations = $v;
        return $this;
    }

    public function getLowAcademicConfidence(): ?int
    {
        return $this->lowAcademicConfidence;
    }
    public function setLowAcademicConfidence(?int $v): static
    {
        $this->lowAcademicConfidence = $v;
        return $this;
    }

    public function getClassAttendance(): ?int
    {
        return $this->classAttendance;
    }
    public function setClassAttendance(?int $v): static
    {
        $this->classAttendance = $v;
        return $this;
    }

    public function getAnxietyTension(): ?int
    {
        return $this->anxietyTension;
    }
    public function setAnxietyTension(?int $v): static
    {
        $this->anxietyTension = $v;
        return $this;
    }

    public function getIrritability(): ?int
    {
        return $this->irritability;
    }
    public function setIrritability(?int $v): static
    {
        $this->irritability = $v;
        return $this;
    }

    public function getSubjectConfidence(): ?int
    {
        return $this->subjectConfidence;
    }
    public function setSubjectConfidence(?int $v): static
    {
        $this->subjectConfidence = $v;
        return $this;
    }
}
