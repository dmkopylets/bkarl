<?php

declare(strict_types=1);

namespace App\Entity\User;

use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Features\User\Repository\UserRepository;
use App\Application\Service\Traits\Timestamps;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: "`user`")]
#[ORM\HasLifecycleCallbacks]
class User implements UserInterface, PasswordAuthenticatedUserInterface, \JsonSerializable
{
    use Timestamps;

    const STATUS = [
        'ACTIVE' => 10,
        'DELETED' => 0
    ];

    const ROLES = [
        'DRIVER' => 'driver',
        'PASSANGER' => 'passanger'
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    public int $id;

    #[Assert\Email]
    #[ORM\Column(type: "string", length: 50, nullable: true)]
    public ?string $email;

    #[ORM\Column(name: 'phone', type: "string", unique: true, nullable: true)]
    public ?string $phone;

    #[Assert\NotBlank]
    #[Assert\Length(min: 7)]
    #[ORM\Column(type: "string")]
    public string $password;

    #[Assert\NotNull]
    #[Assert\Length(max: 25)]
    #[ORM\Column(type: "string", length: 25)]
    public string $firstName = '';

    #[Assert\NotNull]
    #[Assert\Length(max: 25)]
    #[ORM\Column(type: "string", length: 25)]
    public string $lastName = '';

    #[ORM\Column(type: "integer")]
    public int $status;

    #[ORM\Column(type: "string", nullable: true)]
    public string $image = '';

    #[ORM\Column(type: "json")]
    public $roles = [];

    #[ORM\OneToMany(mappedBy: "user", targetEntity: "PhoneConfirm")]
    public $phoneConfirmList;

    #[ORM\Column(type: "string", nullable: true)]
    public ?string $changedPhone;

    #[ORM\Column(type: "string", nullable: true)]
    public ?string $description;

    #[ORM\Column(type: "string", nullable: true)]
    public ?string $car;

    public function getUserIdentifier(): string
    {
        return $this->phone;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER;

        return array_unique($roles);
    }

    public function setRoles(?string $role): self
    {
        $this->roles[] = $role;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function formatPassword(): string
    {
        return base64_encode($this->phone);
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function isRegistered(): bool
    {
        return $this->status === self::STATUS['ACTIVE'];
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS['ACTIVE'];
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
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

    public function getImage()
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getUsername(): string
    {
        return $this->firstName . ' ' . $this->lastName;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }


    public function getChangedPhone(): string
    {
        return $this->changedPhone;
    }


    public function setChangedPhone(?string $changedPhone): self
    {
       $this->changedPhone = $changedPhone;

       return $this;
    }

    public function getPhoneConfirmList()
    {
        return $this->phoneConfirmList;
    }

    public function setPhoneConfirmList($phoneConfirmList): void
    {
        $this->phoneConfirmList = $phoneConfirmList;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getCar(): ?string
    {
        return $this->car;
    }

    public function setCar(?string $car): void
    {
        $this->car = $car;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'phone' => $this->getPhone(),
            'status' => $this->getStatus(),
            'firstName' => $this->getFirstName(),
            'lastName' => $this->getLastName(),
            'description' => $this->getDescription(),
            'car' => $this->getCar()
        ];
    }
}
