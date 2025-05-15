<?php

namespace App\Tests\Model;

use App\Model\Game;
use App\Model\DeckOfCards;
use PHPUnit\Framework\TestCase;

class GameTest extends TestCase
{
    public function testInitialScores()
    {
        $game = new Game();
        $game->dealInitialCards();
        $this->assertGreaterThan(0, $game->getPlayerScore());
        $this->assertGreaterThan(0, $game->getDealerScore());
    }

    public function testPlayerDrawCard()
    {
        $game = new Game();
        $game->dealInitialCards();
        $initialScore = $game->getPlayerScore();
        $game->drawCardForPlayer();
        $this->assertGreaterThan($initialScore, $game->getPlayerScore());
    }

    public function testDealerTurn()
    {
        $game = new Game();
        $game->dealInitialCards();
        $game->dealerTurn();
        $this->assertGreaterThanOrEqual(17, $game->getDealerScore());
    }
}
