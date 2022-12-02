<?php

namespace App\Entity;

use App\Repository\ContactRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;


#[ORM\Entity(repositoryClass: ContactRepository::class)]
class Contact
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(
        message: "Le prénom est obligatoire."
        )]
        #[Assert\Length(
           
            max: 255,
            maxMessage: " Le prénom doit contenir au maximum {{ limit }} caractères." ,
        )]
    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

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
        message: "L'email est obligatoire."
        )]
        #[Assert\Length(
           
            max: 255,
            maxMessage: " L'email doit contenir au maximum {{ limit }} caractères." ,
        )]
        #[Assert\Email(
            message: " Veuillez entrer un email valide .",
        )]
    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[Assert\Length(
           
        max: 255,
        maxMessage: " L'email doit contenir au maximum {{ limit }} caractères." ,
    )]
    #[Assert\Regex(
        pattern: '/^[0-9\-\+\s\(\)]{6,30}$/',
        match: true,
        message: "Veuillez entrer un numéro de téléphone valide."
    )]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tel = null;

    
    #[Assert\NotBlank(
        message: "Le message est obligatoire."
        )]
        #[Assert\Length(
           
            max: 1000,
            maxMessage: " Le message doit contenir au maximum {{ limit }} caractères." ,
        )]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $message = null;

    /**
     * @var \DateTimeImmutable
     */
    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $createdAt = null;


    
    public function getId(): ?int
    {
        return $this->id;
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

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
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

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(?string $tel): self
    {
        $this->tel = $tel;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

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
}
