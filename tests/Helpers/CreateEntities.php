<?php

namespace App\Tests\Helpers;

use App\Entity\Car;
use App\Entity\Colour;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;

trait CreateEntities
{
    public function makeCar(Colour $colour = null): Car {
        $car = new Car();
        $car->setMake('Jeep');
        $car->setModel('Wrangler');
        $car->setColour($colour);
        $car->setBuildAt((new \DateTime())->modify('-1 year'));
        return $car;
    }

    public function makeCarWithoutMake(Colour $colour = null): Car {
        $car = new Car();
        $car->setModel('Wrangler');
        $car->setColour($colour);
        $car->setBuildAt((new \DateTime())->modify('-1 year'));
        return $car;
    }

    public function makeCarWithoutModel(Colour $colour = null): Car {
        $car = new Car();
        $car->setMake('Jeep');
        $car->setColour($colour);
        $car->setBuildAt((new \DateTime())->modify('-1 year'));
        return $car;
    }

    public function makeCarWithoutBuildAt(Colour $colour = null): Car {
        $car = new Car();
        $car->setMake('Jeep');
        $car->setModel('Wrangler');
        $car->setColour($colour);
        return $car;
    }

    public static function loadFixtures($fixture, $entityManager, bool $append = false)
    {
        $loader = new Loader();
        $loader->addFixture($fixture);

        $purger = new ORMPurger($entityManager);
        $executor = new ORMExecutor($entityManager, $purger);
        $executor->execute($loader->getFixtures());
    }
}
