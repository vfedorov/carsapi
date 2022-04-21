<?php

namespace App\DataFixtures;

use App\Entity\Colour;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
	private $baseColours = ['red', 'blue', 'white', 'black'];

	public function load(ObjectManager $manager): void
	{
		foreach ($this->baseColours as $c) {
			$colour = new Colour();
			$colour->setName($c);
            $colour->setEditable(false);
			$manager->persist($colour);
		}
		$manager->flush();
	}
}
