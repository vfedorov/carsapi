<?php

namespace App\Tests\Controller;

use App\DataFixtures\AppFixtures;
use App\Entity\Car;
use App\Entity\Colour;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Tests\Helpers\CreateEntities;

class CarsControllerTest extends WebTestCase
{
    use CreateEntities;

    private $entityManager;
    private $client;
    private Colour $defaultColour;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $container = $this->client->getContainer();
        $doctrine = $container->get('doctrine');
        $this->entityManager = $doctrine->getManager();

        $this->loadFixtures(new AppFixtures(), $this->entityManager);
        $this->defaultColour = $this->entityManager
            ->getRepository(Colour::class)
            ->findOneBy(['name' => 'black']);
    }

    public function testCarCreateDelete(): void
    {
        // create a new car
        $car = $this->makeCar($this->defaultColour);
        $response = $this->client->request('POST', '/cars', $car->toArrayWithoutId());
        $this->assertResponseIsSuccessful();
        $car = $this->entityManager
            ->getRepository(Car::class)
            ->findOneBy([
                'make' => $car->getMake(),
                'model' => $car->getModel(),
                'colour' => $this->defaultColour->getId(),
                // 'buildAt' => $car->getBuildAt()
            ]);
        $this->assertNotNull($car);

        // delete previously created car
        $response = $this->client->request('DELETE', '/cars/' . $car->getId());
        $this->assertResponseIsSuccessful();
        $car = $this->entityManager
            ->getRepository(Car::class)
            ->findOneBy([
                'make' => $car->getMake(),
                'model' => $car->getModel(),
                'colour' => $this->defaultColour->getId(),
                // 'buildAt' => $car->getBuildAt()
            ]);
        $this->assertNull($car);
    }

    public function testCarWithoutColour(): void
    {
        $car = $this->makeCar();
        $response = $this->client->request('POST', '/cars', $car->toArrayWithoutId());
        $this->assertResponseStatusCodeSame('400');
    }

    public function testCarWithoutMake(): void
    {
        $car = $this->makeCarWithoutMake($this->defaultColour);
        $response = $this->client->request('POST', '/cars', $car->toArrayWithoutId());
        $this->assertResponseStatusCodeSame('400');
    }

    public function testCarWithoutModel(): void
    {
        $car = $this->makeCarWithoutModel($this->defaultColour);;
        $response = $this->client->request('POST', '/cars', $car->toArrayWithoutId());
        $this->assertResponseStatusCodeSame('400');
    }

    public function testCarWithoutBuildAt(): void
    {
        $car = $this->makeCarWithoutBuildAt($this->defaultColour);
        $response = $this->client->request('POST', '/cars', $car->toArrayWithoutId());
        $this->assertResponseStatusCodeSame('400');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
