<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;



#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;




    #[Assert\NotBlank(
        message: "L'email est obligatoire."
        )]
        #[Assert\Length(
           
            max: 180,
            maxMessage: " L'email doit contenir au maximum {{ limit }} caractères." ,
        )]
        #[Assert\Email(
            message: " Veuillez entrer un email valide .",
        )]
    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    /**
     * 
     */

    #[ORM\Column]
    private array $roles = [];





    /**
     * @var string The hashed password
     */

    #[Assert\NotBlank(
        message: "Le mot de passe est obligatoire."
        )]
        #[Assert\Length(
            min:8,
            max: 255,
            minMessage: " Le mot de passe doit contenir au minimum {{ limit }} caractères." ,
            maxMessage: " Le mot de passe doit contenir au maximum {{ limit }} caractères." ,
        )]
    #[Assert\Regex(
        pattern: '#^(?=.*[a-zà-ÿ])(?=.*[A-ZÀ-Ỳ])(?=.*[0-9])(?=.*[^a-zà-ÿA-ZÀ-Ỳ0-9]).{8,255}$#',
        match:true,
        message: "Votre mot de passe doit contenir au moins un chiffre, une lettre minuscule, une lettre majuscule et un caractère spécial"
        )]
        #[Assert\NotCompromisedPassword(
            message:"Ce mot de passe est facilement piratable. Veuillez en choisir un autre."
            )]

    #[ORM\Column]
    private ?string $password = null;


    #[Assert\NotBlank(
        message: "Le nom est obligatoire."
        )]
        #[Assert\Length(
           
            max: 255,
            maxMessage: " Le nom doit contenir au maximum {{ limit }} caractères." ,
        )]
    #[ORM\Column(length: 255)]
    private ?string $lastName = null;
    
    #[Assert\NotBlank(
        message: "Le prénom est obligatoire."
        )]
        #[Assert\Length(
           
            max: 255,
            maxMessage: " Le prénom doit contenir au maximum {{ limit }} caractères." ,
        )]
    #[ORM\Column(length: 255)]
    private ?string $firstName = null;
    


    #[ORM\Column(options: array('default' => false))]
    private ?bool $isVerified = null;


    
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tokenForEmailVerification = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $tokenForEmailVerificationExpiresAt = null;


    /**
     * @var \DateTimeImmutable
     */
    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $verifiedAt = null;

    
    /**
     * @var \DateTimeImmutable
     */
    #[Gedmo\Timestampable(on: 'update')]
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;


    public function __construct()
    {
        $this->isVerified = false;
        $this->roles[] = "ROLE_USER";
    }


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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function isIsVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getTokenForEmailVerification(): ?string
    {
        return $this->tokenForEmailVerification;
    }

    public function setTokenForEmailVerification(?string $tokenForEmailVerification): self
    {
        $this->tokenForEmailVerification = $tokenForEmailVerification;

        return $this;
    }

    public function getTokenForEmailVerificationExpiresAt(): ?\DateTimeImmutable
    {
        return $this->tokenForEmailVerificationExpiresAt;
    }

    public function setTokenForEmailVerificationExpiresAt(?\DateTimeImmutable $tokenForEmailVerificationExpiresAt): self
    {
        $this->tokenForEmailVerificationExpiresAt = $tokenForEmailVerificationExpiresAt;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getVerifiedAt(): ?\DateTimeImmutable
    {
        return $this->verifiedAt;
    }

    public function setVerifiedAt(?\DateTimeImmutable $verifiedAt): self
    {
        $this->verifiedAt = $verifiedAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
