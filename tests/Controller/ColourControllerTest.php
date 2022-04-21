<?php

namespace App\Tests\Controller;

use App\DataFixtures\AppFixtures;
use App\Entity\Colour;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Tests\Helpers\CreateEntities;

class ColourControllerTest extends WebTestCase
{
    use CreateEntities;

    private $entityManager;
    private $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $container = $this->client->getContainer();
        $doctrine = $container->get('doctrine');
        $this->entityManager = $doctrine->getManager();
        $this->loadFixtures(new AppFixtures(), $this->entityManager);
    }

    public function testBaseColours(): void
    {
        $countBaseColours = $this->entityManager
            ->getRepository(Colour::class)
            ->count([]);
        $this->assertSame(4, $countBaseColours);
    }

    public function testBaseColourNotEditable(): void
    {
        $colour = $this->entityManager
            ->getRepository(Colour::class)
            ->findOneBy([
                'name' => 'black'
            ]);
        $this->assertNotNull($colour);
        $response = $this->client->request('POST', '/colours/' . $colour->getId() . '/edit', [
            'name' => 'pink',
        ]);
        $this->assertResponseStatusCodeSame(404);
    }

    public function testColourCRUD(): void
    {
        // create a new colour
        $response = $this->client->request('POST', '/colours', [
            'name' => 'purple',
        ]);
        $this->assertResponseIsSuccessful();
        $colour = $this->entityManager
            ->getRepository(Colour::class)
            ->findOneBy([
                'name' => 'purple',
                'editable' => true,
            ]);
        $this->assertNotNull($colour);

        // edit previously created colour
        $response = $this->client->request('POST', '/colours/' . $colour->getId() . '/edit', [
            'name' => 'pink',
        ]);
        $this->assertResponseIsSuccessful();
        $colour = $this->entityManager
            ->getRepository(Colour::class)
            ->findOneBy([
                'name' => 'pink',
                'editable' => true,
            ]);
        $this->assertNotNull($colour);

        // delete previously created colour
        $response = $this->client->request('DELETE', '/colours/' . $colour->getId());
        $this->assertResponseIsSuccessful();
        $colour = $this->entityManager
            ->getRepository(Colour::class)
            ->findOneBy([
                'name' => 'pink',
                'editable' => true,
            ]);
        $this->assertNull($colour);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
    }

}
