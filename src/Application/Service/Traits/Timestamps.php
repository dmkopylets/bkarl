<?php

namespace App\Application\Service\Traits;

use Doctrine\ORM\Mapping as ORM;

trait Timestamps {

    #[ORM\Column(name: "created_at", type: "datetime")]
    private $createdAt;

    #[ORM\Column(name: "update_at", type: "datetime")]
    private $updatedAt;

    #[ORM\PrePersist]
    public function createdAt(): void
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt =  new \DateTime();
    }

    #[ORM\PrePersist]
    public function updatedAt(): void
    {
        $this->updatedAt =  new \DateTime();
    }


    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

}