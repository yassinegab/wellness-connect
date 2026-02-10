<?php

namespace App\Entity\Front_office;

use App\Enum\UserRole;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /* ===================== */
    /*        ID             */
    /* ===================== */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /* ===================== */
    /*        NOM            */
    /* ===================== */
    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: "Le nom est obligatoire")]
    #[Assert\Length(min: 2, max: 100)]
    private ?string $nom = null;

    /* ===================== */
    /*       PRENOM          */
    /* ===================== */
    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: "Le prénom est obligatoire")]
    #[Assert\Length(min: 2, max: 100)]
    private ?string $prenom = null;

    /* ===================== */
    /*        EMAIL          */
    /* ===================== */
    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank(message: "L'email est obligatoire")]
    #[Assert\Email(message: "Email invalide")]
    private ?string $email = null;

    /* ===================== */
    /*      PASSWORD         */
    /* ===================== */
    #[ORM\Column]
    #[Assert\NotBlank(message: "Le mot de passe est obligatoire")]
    #[Assert\Length(
        min: 8,
        minMessage: "Le mot de passe doit contenir au moins 8 caractères"
    )]
    private ?string $password = null;

    /* ===================== */
    /*      TELEPHONE        */
    /* ===================== */
    #[ORM\Column(length: 20)]
    #[Assert\NotBlank(message: "Le téléphone est obligatoire")]
    #[Assert\Regex(
        pattern: "/^[0-9+\s-]{8,20}$/",
        message: "Numéro de téléphone invalide"
    )]
    private ?string $telephone = null;

    /* ===================== */
    /*        ROLE           */
    /* ===================== */
    #[ORM\Column(enumType: UserRole::class)]
    #[Assert\NotNull(message: "Le rôle est obligatoire")]
    private ?UserRole $role = null;

    /* ===================== */
    /*         AGE           */
    /* ===================== */
    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\Range(
        min: 1,
        max: 120,
        notInRangeMessage: "L'âge doit être entre {{ min }} et {{ max }}"
    )]
    private ?int $age = null;

    /* ===================== */
    /*         SEXE          */
    /* ===================== */
    #[ORM\Column(length: 10)]
    #[Assert\Choice(
        choices: ['Homme', 'Femme'],
        message: "Le sexe doit être Homme ou Femme"
    )]
    private ?string $sexe = null;

    /* ===================== */
    /*        POIDS          */
    /* ===================== */
    #[ORM\Column]
    #[Assert\Positive(message: "Le poids doit être positif")]
    private ?float $poids = null;

    /* ===================== */
    /*        TAILLE         */
    /* ===================== */
    #[ORM\Column]
    #[Assert\Positive(message: "La taille doit être positive")]
    private ?float $taille = null;

    /* ===================== */
    /*      HANDICAP         */
    /* ===================== */
    #[ORM\Column]
    private bool $handicap = false;

    /* ===================== */
    /*  INTERFACES SECURITY  */
    /* ===================== */

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getRoles(): array
    {
        return [$this->role?->value ?? 'ROLE_USER'];
    }

    public function eraseCredentials(): void {}

    /* ===================== */
    /*    GETTERS / SETTERS  */
    /* ===================== */

    public function getEmail(): ?string { return $this->email; }
    public function setEmail(string $email): self { $this->email = $email; return $this; }

    public function getPassword(): string { return $this->password; }
    public function setPassword(string $password): self { $this->password = $password; return $this; }

    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): self { $this->nom = $nom; return $this; }

    public function getPrenom(): ?string { return $this->prenom; }
    public function setPrenom(string $prenom): self { $this->prenom = $prenom; return $this; }

    public function getTelephone(): ?string { return $this->telephone; }
    public function setTelephone(string $telephone): self { $this->telephone = $telephone; return $this; }

    public function getRole(): ?UserRole { return $this->role; }
    public function setRole(UserRole $role): self { $this->role = $role; return $this; }

    public function getAge(): ?int { return $this->age; }
    public function setAge(int $age): self { $this->age = $age; return $this; }

    public function getSexe(): ?string { return $this->sexe; }
    public function setSexe(string $sexe): self { $this->sexe = $sexe; return $this; }

    public function getPoids(): ?float { return $this->poids; }
    public function setPoids(float $poids): self { $this->poids = $poids; return $this; }

    public function getTaille(): ?float { return $this->taille; }
    public function setTaille(float $taille): self { $this->taille = $taille; return $this; }

    public function hasHandicap(): bool { return $this->handicap; }
    public function setHandicap(bool $handicap): self { $this->handicap = $handicap; return $this; }

    /* ===================== */
    /*    UTILS              */
    /* ===================== */

    public function getFullName(): string
    {
        return $this->prenom . ' ' . $this->nom;
    }
}
