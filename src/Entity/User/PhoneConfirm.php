<?php

namespace App\Entity\User;

use App\Features\User\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: "`user_phone_confirm`")]
#[ORM\HasLifecycleCallbacks]
class PhoneConfirm
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: "User", inversedBy: "phoneConfirmList")]
    private User $user;

    #[ORM\Column(type: 'string', length: 4)]
    private string $pin;

    #[ORM\Column(type: 'datetime')]
    private DateTime $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime("now");
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getPin(): int
    {
        return $this->pin;
    }


    public function setPin($pin): self
    {
        $this->pin = $pin;
        return $this;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user): self
    {
        $this->user = $user;
        return $this;
    }


}