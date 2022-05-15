<?php

namespace App\Entity;

use App\Repository\TripRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use App\Application\Service\Traits\Timestamps;
use DateTime;
use phpDocumentor\Reflection\Types\Boolean;

#[ORM\Entity(repositoryClass: TripRepository::class)]
#[ORM\Table(name: "`trip`")]
#[ORM\HasLifecycleCallbacks()] 
class Trip implements \JsonSerializable 
{
    use Timestamps;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:'AUTO')]
    #[ORM\Column(type:'integer')]
    private $id;
       
    #[ORM\Column(name: 'driver', type:'integer')]  
    private ?int $driver;
    
    #[ORM\Column(name: 'passanger', type: 'integer')]
    private ?int $passanger;

    #[ORM\Column(name: 'date', type: 'datetime')]
    private \DateTime $date;

    #[ORM\Column(name: 'vacancies', type: 'integer')]
    private ?int $vacancies;

    #[Assert\NotNull]
    #[Assert\Length(max: 50)]
    #[ORM\Column(type: "string", length: 50)]
    public string $from = '';

    #[Assert\NotNull]
    #[Assert\Length(max: 50)]
    #[ORM\Column(type: "string", length: 50)]
    public string $to = '';

    #[ORM\Column(name: 'completed', type: 'boolean')]
    private ?Boolean $completed;
    
    public function getId(): int
    {
        return $this->id;
    }

   /**
   * @return mixed
   */
  public function getDriver()
  {
   return $this->driver;
  }
  /**
   * @param mixed $driver
   */
  public function setDriver($driver)
  {
   $this->driver = $driver;
  }
  /**
   * @return mixed
   */
  public function getPassanger()
  {
   return $this->passanger;
  }
  /**
   * @param mixed $passanger
   */
  public function setPassanger($passanger)
  {
   $this->passanger = $passanger;
  }


  /**
   * @return mixed
   */
  public function getVacancies()
  {
   return $this->vacancies;
  }
  /**
   * @param mixed $vacancies
   */
  public function setVacancies($vacancies)
  {
   $this->vacancies = $vacancies;
  }


/**
   * @return mixed
   */
  public function getDate(): ?\DateTime
  {
   return $this->date;
  }

  /**
   * @param \DateTime $date
   * @return Trip
   */
  public function setDate(\DateTime $date): self
  {
   $this->date = $date;
   return $this;
  }



  public function getFrom(): string
  {
      return $this->from;
  }

  public function setFrom(string $from): self
  {
      $this->from = $from;
      return $this;
  }

  public function getTo(): string
  {
      return $this->to;
  }

  public function setTo(string $to): self
  {
      $this->from = $to;
      return $this;
  }


  public function getCompleted(): bool
  {
    return $this->$completed;
    
  }

  public function setCompleted(string $completed): self
  {
      $this->from = $completed;
      return $this;
  }


    
  /**
   * @throws \Exception
   * @ORM\PrePersist()
   */
  public function beforeSave(){

    $this->create_date = new \DateTime('now', new \DateTimeZone('Europe/Zaporozhye'));
   }
 
   /**
    * Specify data which should be serialized to JSON
    * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
    * @return mixed data which can be serialized by <b>json_encode</b>,
    * which is a value of any type other than a resource.
    * @since 5.4.0
    */
   public function jsonSerialize()
   {
    return [
        'id' => $this->getId(),
        'driver' => $this->getDriver(),
        'passanger' => $this->getPassanger(),
        'vacancies' => $this->getVacancies(),
        'date' => $this->getDate(),
        'from' => $this->getFrom(),
        'to' => $this->getTo(),
        'completed' => $this->GetCompleted()
    ];
   }
    



    
    
}
