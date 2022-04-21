<?php

namespace App\Tests\Entity;

use App\Entity\Car;
use PHPUnit\Framework\TestCase;

class CarTest extends TestCase
{
    public function testCarBuildDateLessThanOrEqualNow()
    {
        $car = new Car();
        $car->setBuildAt((new \DateTime('now'))->modify('-1 year'));
        self::assertLessThanOrEqual(new \DateTime('now'), $car->getBuildAt(), 'Car can\'t be newer than now');
    }

    public function testCarBuildDateGreaterThan4Years()
    {
        $car = new Car();
        $car->setBuildAt((new \DateTime('now'))->modify('-1 year'));
        self::assertGreaterThanOrEqual((new \DateTime('now'))->modify('-4 year'), $car->getBuildAt(), 'Car can\'t be older than 4 years');
    }
}
