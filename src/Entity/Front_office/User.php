<?php

namespace App\Entity\Front_office;


use App\Enum\UserRole;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use App\Entity\Front_office\DossierMedical;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    // ================= IDENTITÉ =================
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

   #[ORM\Column(type: 'json')]
    private array $roles = []; // ✅ INITIALISATION OBLIGATOIRE

    #[ORM\Column(type: 'string', length: 20, enumType: UserRole::class)]
    private UserRole $userRole;

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 50)]
    private ?string $nom = null;

    #[ORM\Column(length: 50)]
    private ?string $prenom = null;

    #[ORM\Column(length: 20)]
    private ?string $telephone = null;

#[ORM\Column(type: "datetime", nullable: true)]
private ?\DateTimeInterface $created_at = null;

    // ================= INFOS PHYSIQUES =================
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $age = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $poids = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $taille = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $sexe = null;

    #[ORM\Column(type: 'boolean')]
    private bool $handicap = false;

    // ================= RELATION DOSSIERS MEDICAUX =================
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: DossierMedical::class, orphanRemoval: true)]
    private Collection $dossiersMedicaux;

    // ================= CONSTRUCTEUR =================
    public function __construct()
    {
        $this->created_at = new \DateTime(); // initialise la date actuelle
        $this->roles = [];                   // roles par défaut
        $this->dossiersMedicaux = new ArrayCollection();
        $this->setUserRole(UserRole::PATIENT); // rôle par défaut
        
    
    }

    // ================= GETTERS / SETTERS =================
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    // ================= RÔLES =================
    public function getRoles(): array
    {
        return array_unique($this->roles);
    }

    public function getUserRole(): UserRole
    {
        return $this->userRole;
    }

    public function setUserRole(UserRole $userRole): self
    {
        $this->userRole = $userRole;

        $this->roles = match ($userRole) {
            UserRole::PATIENT => ['ROLE_PATIENT'],
            UserRole::MEDECIN => ['ROLE_MEDECIN'],
            UserRole::ADMIN   => ['ROLE_ADMIN'],
        };

        return $this;
    }

    // ================= PASSWORD =================
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function eraseCredentials(): void
    {
        // Rien à faire
    }

    // ================= NOM / PRENOM / TELEPHONE =================
    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;
        return $this;
    }

    // ================= CREATED_AT =================
    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->created_at;
    }

    // ================= INFOS PHYSIQUES =================
    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(?int $age): self
    {
        $this->age = $age;
        return $this;
    }

    public function getPoids(): ?float
    {
        return $this->poids;
    }

    public function setPoids(?float $poids): self
    {
        $this->poids = $poids;
        return $this;
    }

    public function getTaille(): ?float
    {
        return $this->taille;
    }

    public function setTaille(?float $taille): self
    {
        $this->taille = $taille;
        return $this;
    }

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(?string $sexe): self
    {
        $this->sexe = $sexe;
        return $this;
    }

    // ================= HANDICAP =================
    public function isHandicap(): bool
    {
        return $this->handicap;
    }

    public function setHandicap(bool $handicap): self
    {
        $this->handicap = $handicap;
        return $this;
    }

    // ================= DOSSIERS MEDICAUX =================
    public function getDossiersMedicaux(): Collection
    {
        return $this->dossiersMedicaux;
    }

    public function addDossierMedical(DossierMedical $dossier): self
    {
        if (!$this->dossiersMedicaux->contains($dossier)) {
            $this->dossiersMedicaux->add($dossier);
            $dossier->setUser($this);
        }

        return $this;
    }

    public function removeDossierMedical(DossierMedical $dossier): self
    {
        if ($this->dossiersMedicaux->removeElement($dossier)) {
            if ($dossier->getUser() === $this) {
                $dossier->setUser(null);
            }
        }

        return $this;
    }
}
