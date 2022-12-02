<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;



#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'Impossible de creer un compte avec cet email')]
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

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Post::class)]
    private Collection $posts;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Comment::class, orphanRemoval: true)]
    private Collection $comments;


    public function __construct()
    {
        $this->isVerified = false;
        $this->roles[] = "ROLE_USER";
        $this->posts = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
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
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
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

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
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

    /**
     * @return Collection<int, Post>
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts->add($post);
            $post->setUser($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getUser() === $this) {
                $post->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

        return $this;
    }
}
