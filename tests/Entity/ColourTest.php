<?php

namespace Tests\Entity;

use App\Entity\Colour;
use PHPUnit\Framework\TestCase;

class ColourTest extends TestCase
{
    public function testColour() {
        $colour = new Colour();
        $colour->setName('black');
        self::assertSame('black', $colour->getName());
    }
}
