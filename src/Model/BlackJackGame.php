<?php

namespace App\Model;

class BlackJackGame
{
    private array $deck = [];
    private array $playerHands = [];
    private array $playerScores = [];
    private array $handStatus = [];
    private array $dealerHand = [];
    private int $dealerScore = 0;
    private int $handCount = 1;
    private array $cardsDrawn = [];

    public function __construct(int $handCount = 1)
    {
        $this->handCount = $handCount;
        $this->initDeck();
        $this->startNewGame();
    }

    private function initDeck()
    {
        $suits = ['♠', '♥', '♦', '♣'];
        $ranks = ['2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K', 'A'];
        $this->deck = [];

        foreach ($suits as $suit) {
            foreach ($ranks as $rank) {
                $this->deck[] = "$rank$suit";
            }
        }
        shuffle($this->deck);
    }

    public function startNewGame()
    {
        $this->playerHands = [];
        $this->playerScores = [];
        $this->handStatus = [];
        $this->dealerHand = [];
        $this->dealerScore = 0;
        $this->cardsDrawn = [];

        for ($i = 0; $i < $this->handCount; $i++) {
            $this->playerHands[$i] = [];
            $this->playerScores[$i] = 0;
            $this->handStatus[$i] = 'playing';
        }

        for ($i = 0; $i < 2; $i++) {
            for ($hand = 0; $hand < $this->handCount; $hand++) {
                $this->dealCardToPlayerHand($hand);
            }
            $this->dealCardToDealer();
        }
    }

    private function dealCardToPlayerHand(int $handIndex)
    {
        $card = array_pop($this->deck);
        $this->playerHands[$handIndex][] = $card;
        $this->cardsDrawn[$card] = ($this->cardsDrawn[$card] ?? 0) + 1;
        $this->playerScores[$handIndex] = $this->calculateScore($this->playerHands[$handIndex]);

        if ($this->playerScores[$handIndex] == 21 && count($this->playerHands[$handIndex]) == 2) {
            $this->handStatus[$handIndex] = 'blackjack';
        } elseif ($this->playerScores[$handIndex] > 21) {
            $this->handStatus[$handIndex] = 'bust';
        }
    }

    private function dealCardToDealer()
    {
        $card = array_pop($this->deck);
        $this->dealerHand[] = $card;
        $this->cardsDrawn[$card] = ($this->cardsDrawn[$card] ?? 0) + 1;
        $this->dealerScore = $this->calculateScore($this->dealerHand);
    }

    private function calculateScore(array $hand): int
    {
        $score = 0;
        $aces = 0;

        foreach ($hand as $card) {
            // Exempel: "10♠" eller "A♥"
            $rank = substr($card, 0, strlen($card) - 3);
            if (!$rank) {
                // Om kortet är t.ex. "10♠", tar vi 2 tecken, annars 1 tecken
                if (strlen($card) == 3) {
                    $rank = substr($card, 0, 2);
                } else {
                    $rank = substr($card, 0, 1);
                }
            }

            if (is_numeric($rank)) {
                $score += intval($rank);
            } elseif (in_array($rank, ['J', 'Q', 'K'])) {
                $score += 10;
            } elseif ($rank == 'A') {
                $aces++;
                $score += 11;
            }
        }

        while ($score > 21 && $aces > 0) {
            $score -= 10;
            $aces--;
        }

        return $score;
    }

    public function hit(int $handIndex): void
    {
        if (!isset($this->handStatus[$handIndex]) || $this->handStatus[$handIndex] != 'playing') {
            return;
        }
        $this->dealCardToPlayerHand($handIndex);
    }

    public function stand(int $handIndex): void
    {
        if (!isset($this->handStatus[$handIndex]) || $this->handStatus[$handIndex] != 'playing') {
            return;
        }
        $this->handStatus[$handIndex] = 'stand';
    }

    public function allHandsFinished(): bool
    {
        foreach ($this->handStatus as $status) {
            if ($status == 'playing') {
                return false;
            }
        }
        return true;
    }

    public function dealerTurn(): void
    {
        while ($this->dealerScore < 17) {
            $this->dealCardToDealer();
        }
    }

    public function getResults(): array
    {
        $results = [];

        foreach ($this->playerHands as $i => $hand) {
            $handScore = $this->playerScores[$i];
            $handStatus = $this->handStatus[$i];

            if ($handStatus == 'bust') {
                $results[$i] = 'Förlorade (Bust)';
                continue;
            }

            if ($handStatus == 'blackjack' && $this->dealerScore != 21) {
                $results[$i] = 'Vann med Blackjack!';
                continue;
            }

            if ($this->dealerScore > 21) {
                $results[$i] = 'Vann, dealern gick bust';
                continue;
            }

            if ($handScore > $this->dealerScore) {
                $results[$i] = 'Vann';
            } elseif ($handScore < $this->dealerScore) {
                $results[$i] = 'Förlorade';
            } else {
                $results[$i] = 'Oavgjort (Push)';
            }
        }

        return $results;
    }

    // Getters
    public function getPlayerHands(): array
    {
        return $this->playerHands;
    }
    public function getPlayerScores(): array
    {
        return $this->playerScores;
    }
    public function getHandStatus(): array
    {
        return $this->handStatus;
    }
    public function getDealerHand(): array
    {
        return $this->dealerHand;
    }
    public function getDealerScore(): int
    {
        return $this->dealerScore;
    }
    public function getDeckCount(): int
    {
        return count($this->deck);
    }
    public function getCardsDrawn(): array
    {
        return $this->cardsDrawn;
    }
    public function getDrawnCardStats(): array
    {


        $allCards = [];

        // Samla alla kort från spelarens händer
        foreach ($this->playerHands as $hand) {
            foreach ($hand as $card) {
                $allCards[] = $card;
            }
        }

        // Samla kort från dealern
        foreach ($this->dealerHand as $card) {
            $allCards[] = $card;
        }


        $counts = [];
        foreach ($allCards as $card) {
            if (!isset($counts[$card])) {
                $counts[$card] = 0;
            }
            $counts[$card]++;
        }

        return $counts;
    }
}
