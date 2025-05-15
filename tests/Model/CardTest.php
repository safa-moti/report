<?php

namespace App\Tests\Model;

use App\Model\Card;
use PHPUnit\Framework\TestCase;

class CardTest extends TestCase
{
    public function testCardValue()
    {
        $card = new Card('A', '♠');
        $this->assertEquals(11, $card->getValue());

        $card = new Card('K', '♦');
        $this->assertEquals(10, $card->getValue());

        $card = new Card('5', '♣');
        $this->assertEquals(5, $card->getValue());
    }

    public function testCardStringRepresentation()
    {
        $card = new Card('A', '♠');
        $this->assertEquals('A of ♠', (string) $card);
    }
}
