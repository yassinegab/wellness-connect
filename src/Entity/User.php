<?php

namespace App\Entity;

use App\Repository\UserRepository;
use App\Enum\UserRole;
use App\Entity\Front_office\DossierMedical;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank(message: "L'email est obligatoire")]
    #[Assert\Email(message: "L'email '{{ value }}' n'est pas valide")]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom est obligatoire")]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: "Le nom doit contenir au moins {{ limit }} caractères",
        maxMessage: "Le nom ne peut pas dépasser {{ limit }} caractères"
    )]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le prénom est obligatoire")]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: "Le prénom doit contenir au moins {{ limit }} caractères",
        maxMessage: "Le prénom ne peut pas dépasser {{ limit }} caractères"
    )]
    private ?string $prenom = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[Assert\Regex(
        pattern: "/^[0-9+\s-]*$/",
        message: "Le numéro de téléphone n'est pas valide"
    )]
    private ?string $telephone = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * @var Collection<int, UserWellBeingData>
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserWellBeingData::class)]
    private Collection $userWellBeingData;

    /**
     * @var Collection<int, ChatbotMessage>
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: ChatbotMessage::class, cascade: ['persist', 'remove'])]
    private Collection $chatbotMessages;

    /**
     * @var Collection<int, Journal>
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Journal::class, cascade: ['persist', 'remove'])]
    private Collection $journals;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->dossiersMedicaux = new ArrayCollection();
        $this->userWellBeingData = new ArrayCollection();
        $this->chatbotMessages = new ArrayCollection();
        $this->journals = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function eraseCredentials(): void {}

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): static
    {
        $this->telephone = $telephone;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getFullName(): string
    {
        return $this->prenom . ' ' . $this->nom;
    }

    public function isPacient(): bool
    {
        return in_array('ROLE_PATIENT', $this->roles);
    }

    public function isMedecin(): bool
    {
        return in_array('ROLE_MEDECIN', $this->roles);
    }

    public function getRoleLabel(): string
    {
        if ($this->isMedecin()) {
            return 'Médecin';
        }
        if ($this->isPacient()) {
            return 'Patient';
        }
        return 'Utilisateur';
    }

    /**
     * @return Collection<int, UserWellBeingData>
     */
    public function getUserWellBeingData(): Collection
    {
        return $this->userWellBeingData;
    }

    public function addUserWellBeingData(UserWellBeingData $data): static
    {
        if (!$this->userWellBeingData->contains($data)) {
            $this->userWellBeingData->add($data);
            $data->setUser($this);
        }

        return $this;
    }

    public function removeUserWellBeingData(UserWellBeingData $data): static
    {
        if ($this->userWellBeingData->removeElement($data)) {
            // set the owning side to null (unless already changed)
            if ($data->getUser() === $this) {
                $data->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ChatbotMessage>
     */
    public function getChatbotMessages(): Collection
    {
        return $this->chatbotMessages;
    }

    public function addChatbotMessage(ChatbotMessage $chatbotMessage): static
    {
        if (!$this->chatbotMessages->contains($chatbotMessage)) {
            $this->chatbotMessages->add($chatbotMessage);
            $chatbotMessage->setUser($this);
        }

        return $this;
    }

    public function removeChatbotMessage(ChatbotMessage $chatbotMessage): static
    {
        if ($this->chatbotMessages->removeElement($chatbotMessage)) {
            // set the owning side to null (unless already changed)
            if ($chatbotMessage->getUser() === $this) {
                $chatbotMessage->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Journal>
     */
    public function getJournals(): Collection
    {
        return $this->journals;
    }

    public function addJournal(Journal $journal): static
    {
        if (!$this->journals->contains($journal)) {
            $this->journals->add($journal);
            $journal->setUser($this);
        }

        return $this;
    }

    public function removeJournal(Journal $journal): static
    {
        if ($this->journals->removeElement($journal)) {
            // set the owning side to null (unless already changed)
            if ($journal->getUser() === $this) {
                $journal->setUser(null);
            }
        }

        return $this;
    }

    // ================= INFOS PHYSIQUES & ROLES =================
    public function getUserRole(): ?UserRole
    {
        return $this->userRole;
    }

    public function setUserRole(?UserRole $userRole): static
    {
        $this->userRole = $userRole;

        if ($userRole) {
            $roleString = match ($userRole) {
                UserRole::PATIENT => 'ROLE_PATIENT',
                UserRole::MEDECIN => 'ROLE_MEDECIN',
                UserRole::ADMIN   => 'ROLE_ADMIN',
            };

            if (!in_array($roleString, $this->roles)) {
                $this->roles[] = $roleString;
            }
        }

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(?int $age): static
    {
        $this->age = $age;
        return $this;
    }

    public function getPoids(): ?float
    {
        return $this->poids;
    }

    public function setPoids(?float $poids): static
    {
        $this->poids = $poids;
        return $this;
    }

    public function getTaille(): ?float
    {
        return $this->taille;
    }

    public function setTaille(?float $taille): static
    {
        $this->taille = $taille;
        return $this;
    }

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(?string $sexe): static
    {
        $this->sexe = $sexe;
        return $this;
    }

    public function isHandicap(): bool
    {
        return $this->handicap;
    }

    public function setHandicap(bool $handicap): static
    {
        $this->handicap = $handicap;
        return $this;
    }

    /**
     * @return Collection<int, DossierMedical>
     */
    public function getDossiersMedicaux(): Collection
    {
        return $this->dossiersMedicaux;
    }

    public function addDossierMedical(DossierMedical $dossierMedical): static
    {
        if (!$this->dossiersMedicaux->contains($dossierMedical)) {
            $this->dossiersMedicaux->add($dossierMedical);
            $dossierMedical->setUser($this);
        }

        return $this;
    }

    public function removeDossierMedical(DossierMedical $dossierMedical): static
    {
        if ($this->dossiersMedicaux->removeElement($dossierMedical)) {
            // set the owning side to null (unless already changed)
            if ($dossierMedical->getUser() === $this) {
                $dossierMedical->setUser(null);
            }
        }

        return $this;
    }
}
