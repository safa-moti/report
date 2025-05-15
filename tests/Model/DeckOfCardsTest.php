<?php

namespace App\Tests\Model;

use App\Model\DeckOfCards;
use PHPUnit\Framework\TestCase;

class DeckOfCardsTest extends TestCase
{
    /**
     * Testar att kortleken innehåller 52 kort vid skapandet.
     */
    public function testDeckHas52Cards()
    {
        $deck = new DeckOfCards();
        $this->assertCount(52, $deck->getCards());
    }

    /**
     * Testar att ett kort dras från kortleken.
     */
    public function testDrawCard()
    {
        $deck = new DeckOfCards();
        $cardBefore = count($deck->getCards());
        $deck->drawCard();
        $cardAfter = count($deck->getCards());
        $this->assertEquals($cardBefore - 1, $cardAfter);
    }
}
