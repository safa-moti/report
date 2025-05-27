<?php

namespace App\Service;

class BlackJackGame
{
    private array $deck = [];
    private array $playerHands = [];
    private array $dealerHand = [];
    private array $drawnCards = [];

    public function __construct(int $handCount = 1)
    {
        if ($handCount < 1 || $handCount > 3) {
            throw new \InvalidArgumentException("Hand count must be between 1 and 3.");
        }

        $this->createDeck();
        shuffle($this->deck);

        // Dela ut 2 kort till varje hand
        for ($i = 0; $i < $handCount; $i++) {
            $hand = [$this->drawCard(), $this->drawCard()];
            $this->playerHands[$i] = $hand;
        }

        // Dela ut till dealer
        $this->dealerHand = [$this->drawCard(), $this->drawCard()];
    }

    private function createDeck(): void
    {
        $ranks = ['2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K', 'A'];
        $suits = ['♠', '♥', '♦', '♣'];

        foreach ($suits as $suit) {
            foreach ($ranks as $rank) {
                $this->deck[] = "$rank$suit";
            }
        }
    }

    private function drawCard(): string
    {
        $card = array_pop($this->deck);
        if ($card !== null) {
            $this->drawnCards[] = $card;
        }
        return $card;
    }

    public function getPlayerHands(): array
    {
        return $this->playerHands;
    }

    public function getDealerHand(): array
    {
        return $this->dealerHand;
    }

    public function getPlayerScores(): array
    {
        $scores = [];
        foreach ($this->playerHands as $hand) {
            $scores[] = $this->calculateScore($hand);
        }
        return $scores;
    }

    public function getDealerScore(): int
    {
        return $this->calculateScore($this->dealerHand);
    }

    public function getDeckCount(): int
    {
        return count($this->deck);
    }

    public function getDrawnCardStats(): array
    {
        $stats = [];
        foreach ($this->drawnCards as $card) {
            $stats[$card] = ($stats[$card] ?? 0) + 1;
        }
        ksort($stats);
        return $stats;
    }

    private function calculateScore(array $hand): int
    {
        $score = 0;
        $aces = 0;
        foreach ($hand as $card) {
            $rank = preg_replace('/[^\d\w]/', '', $card);
            if (is_numeric($rank)) {
                $score += (int)$rank;
            } elseif (in_array($rank, ['J', 'Q', 'K'])) {
                $score += 10;
            } elseif ($rank === 'A') {
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
}
