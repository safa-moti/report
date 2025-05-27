<?php

namespace App\Tests\Model;

use PHPUnit\Framework\TestCase;
use App\Model\BlackJackGame;

class BlackJackGameTest extends TestCase
{
    public function testGameInitializesWithCorrectHandCount()
    {
        $game = new BlackJackGame(3);
        $this->assertCount(3, $game->getPlayerHands());
        $this->assertCount(3, $game->getPlayerScores());
        $this->assertCount(3, $game->getHandStatus());
    }

    public function testGameStartsWithTwoCardsEach()
    {
        $game = new BlackJackGame(1);
        $hand = $game->getPlayerHands()[0];
        $dealer = $game->getDealerHand();
        $this->assertCount(2, $hand);
        $this->assertGreaterThanOrEqual(1, count($dealer)); // Dealern har minst 1 kort
    }

    public function testHitAddsCard()
    {
        $game = new BlackJackGame(1);

        // Loopa tills handen inte är blackjack eller bust för att kunna hit:a
        while (in_array($game->getHandStatus()[0], ['blackjack', 'bust'])) {
            $game = new BlackJackGame(1);
        }

        $initialCount = count($game->getPlayerHands()[0]);
        $game->hit(0);
        $newCount = count($game->getPlayerHands()[0]);

        $this->assertGreaterThan($initialCount, $newCount);
    }

    public function testStandChangesStatus()
    {
        $game = new BlackJackGame(1);
        $game->stand(0);
        $this->assertEquals('stand', $game->getHandStatus()[0]);
    }

    public function testAllHandsFinished()
    {
        $game = new BlackJackGame(1);

        // Initialt ska alla händer inte vara klara
        $this->assertFalse($game->allHandsFinished());

        // Ändra status till något som indikerar att handen är klar, t.ex. 'stand'
        $this->setPrivateProperty($game, 'handStatus', ['stand']);

        // Nu ska allHandsFinished() returnera true eftersom alla händer är "stand"
        $this->assertTrue($game->allHandsFinished());
    }

    public function testDealerTurnStopsAt17OrMore()
    {
        $game = new BlackJackGame(1);
        $game->stand(0);
        $this->assertTrue($game->allHandsFinished());

        $game->dealerTurn();
        $this->assertGreaterThanOrEqual(17, $game->getDealerScore());
    }

    public function testGetResultsCoversWinLoseDraw()
    {
        $game = new BlackJackGame(1);

        // Sätt upp en hand med känd poäng och status
        $this->setPrivateProperty($game, 'playerHands', [['10♠', '9♦']]);
        $this->setPrivateProperty($game, 'playerScores', [19]);
        $this->setPrivateProperty($game, 'handStatus', ['stand']);
        $this->setPrivateProperty($game, 'dealerHand', ['10♣', '7♠']);
        $this->setPrivateProperty($game, 'dealerScore', 17);

        $results = $game->getResults();
        $this->assertEquals('Vann', $results[0]);
    }

    public function testBustResultsInLoss()
    {
        $game = new BlackJackGame(1);
        $this->setPrivateProperty($game, 'playerHands', [['10♠', '9♦', '5♣']]);
        $this->setPrivateProperty($game, 'playerScores', [24]);
        $this->setPrivateProperty($game, 'handStatus', ['bust']);
        $this->setPrivateProperty($game, 'dealerScore', 18);

        $results = $game->getResults();
        $this->assertStringContainsString('Förlorade', $results[0]);
    }

    public function testBlackjackResultsInWin()
    {
        $game = new BlackJackGame(1);
        $this->setPrivateProperty($game, 'playerHands', [['A♠', 'K♦']]);
        $this->setPrivateProperty($game, 'playerScores', [21]);
        $this->setPrivateProperty($game, 'handStatus', ['blackjack']);
        $this->setPrivateProperty($game, 'dealerScore', 18);

        $results = $game->getResults();
        $this->assertEquals('Vann med Blackjack!', $results[0]);
    }

    public function testDrawResultsInPush()
    {
        $game = new BlackJackGame(1);
        $this->setPrivateProperty($game, 'playerHands', [['10♠', '7♦']]);
        $this->setPrivateProperty($game, 'playerScores', [17]);
        $this->setPrivateProperty($game, 'handStatus', ['stand']);
        $this->setPrivateProperty($game, 'dealerScore', 17);

        $results = $game->getResults();
        $this->assertEquals('Oavgjort (Push)', $results[0]);
    }

    public function testCardDrawnStats()
    {
        $game = new BlackJackGame(1);
        $stats = $game->getDrawnCardStats();
        $this->assertIsArray($stats);
        $this->assertNotEmpty($stats);
    }

    /**
     * Hjälpmetod för att manipulera privata egenskaper.
     *
     * @param object $object Objekt att ändra på
     * @param string $property Egenskapens namn
     * @param mixed $value Värdet att sätta
     */
    private function setPrivateProperty($object, string $property, $value): void
    {
        $reflection = new \ReflectionClass($object);
        $prop = $reflection->getProperty($property);
        $prop->setAccessible(true);
        $prop->setValue($object, $value);
    }
}
