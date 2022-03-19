<?php

namespace App\Entity;

use DateTimeInterface;
use App\Enums\VehicleTypeEnum;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\VehicleRepository;
use App\Enums\VehicleTypeEnum as EnumsVehicleTypeEnum;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: VehicleRepository::class)]
class Vehicle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'datetime')]
    #[Assert\Type("\DateTimeInterface")]
    private $date_added;

    #[ORM\Column(type: "string", enumType: EnumsVehicleTypeEnum::class)]
    #[Assert\Choice(callback: [VehicleTypeEnum::class, 'cases'])]
    private EnumsVehicleTypeEnum $type;

    #[ORM\Column(type: 'decimal', precision: 20, scale: 2)]
    #[Assert\NotBlank]
    #[Assert\Positive]
    private $msrp;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank]
    #[Assert\GreaterThanOrEqual(1900, message: 'The year must be at least {{ compared_value }} or newer')]
    #[Assert\Length(min:4, max:4, minMessage:'The year must be a valid {{ limit }} digit year')]
    private $year;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    private $make;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    private $model;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank]
    private $miles;

    #[ORM\Column(type: 'string', length: 20)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 17, max: 20)]
    private $vin;

    #[ORM\Column(type: 'boolean')]
    private $deleted;

    public function __construct()
    {
        //default value
        $this->deleted = false;
        $this->type = VehicleTypeEnum::NEW;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateAdded(): ?DateTimeInterface
    {
        return $this->date_added;
    }

    public function setDateAdded(DateTimeInterface $date_added): self
    {
        $this->date_added = $date_added;

        return $this;
    }

    public function getType(): ?VehicleTypeEnum
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        if ($type == VehicleTypeEnum::USED) {
            $this->type = VehicleTypeEnum::USED;
        } else {
            $this->type = VehicleTypeEnum::NEW;
        }

        return $this;
    }

    public function getMsrp(): ?float
    {
        return $this->msrp;
    }

    public function setMsrp(float $msrp): self
    {
        $this->msrp = $msrp;

        return $this;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(int $year): self
    {
        $this->year = $year;

        return $this;
    }

    public function getMake(): ?string
    {
        return $this->make;
    }

    public function setMake(string $make): self
    {
        $this->make = $make;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function getMiles(): ?int
    {
        return $this->miles;
    }

    public function setMiles(int $miles): self
    {
        $this->miles = $miles;

        return $this;
    }

    public function getVin(): ?string
    {
        return $this->vin;
    }

    public function setVin(string $vin): self
    {
        $this->vin = $vin;

        return $this;
    }

    public function getDeleted(): ?bool
    {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted): self
    {
        $this->deleted = $deleted;

        return $this;
    }
}
