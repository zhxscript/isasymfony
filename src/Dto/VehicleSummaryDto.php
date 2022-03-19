<?php

namespace App\Dto;

use DateTime;
use DateTimeInterface;
use App\Enums\VehicleTypeEnum;

class VehicleSummaryDto
{
    private string $id;
    private string $model;
    private string $make;
    private VehicleTypeEnum $type;
    private float $msrp;
    private int $year;
    private int $miles;
    private string $vin;
    private DateTimeInterface $date_added;
    
    static function of(string $id, string $model, string $make, VehicleTypeEnum $type, float $msrp, string $vin, int $miles, DateTimeInterface $dateAdded): VehicleSummaryDto
    {
        $dto = new VehicleSummaryDto();
        $dto->setId($id)
        ->setType($type)
        ->setModel($model)
        ->setMake($make)
        ->setMiles($miles)
        ->setVin($vin)
        ->setMsrp($msrp)
        ->setDateAdded($dateAdded);

        return $dto;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return VehicleSummaryDto
     */
    public function setId(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @param string $model
     */
    public function setModel(string $model): self
    {
        $this->model = $model;
        return $this;
    }

    /**
     * @return string
     */
    public function getModel(): string
    {
        return $this->model;
    }

    /**
     * @param string $make
     */
    public function setMake(string $make): self
    {
        $this->make = $make;
        return $this;
    }

    /**
     * @return string
     */
    public function getMake(): string
    {
        return $this->make;
    }

    public function getType(): ?VehicleTypeEnum
    {
        return $this->type;
    }

    public function setType(VehicleTypeEnum $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getMsrp(): ?float
    {
        return $this->msrp;
    }

    public function setMsrp(string $msrp): self
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

    public function getDateAdded(): ?\DateTimeInterface
    {
        return $this->date_added;
    }

    public function setDateAdded(\DateTimeInterface $date_added): self
    {
        $this->date_added = $date_added;

        return $this;
    }
}
