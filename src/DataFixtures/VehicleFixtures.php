<?php

namespace App\DataFixtures;

use DateTime;
use Faker\Factory;
use App\Entity\Vehicle;
use Faker\Provider\Fakecar;
use App\Enums\VehicleTypeEnum;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class VehicleFixtures extends Fixture
{
    /** @var Generator */
    protected $faker;

    public function load(ObjectManager $manager): void
    {
        $this->faker = Factory::create();
        $this->faker->addProvider(new Fakecar($this->faker));

        $max = 25;

        for ($i = 0; $i < $max; $i++) {

            $fake_vehicle = $this->faker->vehicleArray();

            $vehicle = new Vehicle();
            $vehicle->setDateAdded(new DateTime(sprintf('-%d days', rand(1, 100))));
            $vehicle->setType($this->faker->randomElement([VehicleTypeEnum::NEW->value, VehicleTypeEnum::USED->value]));
            $vehicle->setMake($fake_vehicle['brand']);
            $vehicle->setModel($fake_vehicle['model']);
            $vehicle->setMiles($this->faker->numberBetween(5, 97653));
            $vehicle->setVin($this->faker->vin);
            $vehicle->setYear($this->faker->biasedNumberBetween(1998, 2017, 'sqrt'));
            $vehicle->setMsrp($this->faker->randomFloat(2, 19000, 75000));
            $vehicle->setDeleted($this->faker->boolean(32));
            $manager->persist($vehicle);
        }
        $manager->flush();
    }
}
