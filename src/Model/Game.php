<?php

namespace App\Model;

class Game
{
    private array $deck = [];
    private array $playerHand = [];
    private array $dealerHand = [];

    private int $playerScore = 0;
    private int $dealerScore = 0;

    private bool $gameOver = false;
    private ?string $result = null;

    public function __construct()
    {
        $this->resetDeck();
    }

    private function resetDeck(): void
    {
        $suits = ['clubs', 'diamonds', 'hearts', 'spades'];
        $values = ['2', '3', '4', '5', '6', '7', '8', '9', '10', 'jack', 'queen', 'king', 'ace'];

        $this->deck = [];
        foreach ($suits as $suit) {
            foreach ($values as $value) {
                $this->deck[] = "{$value}_of_{$suit}";
            }
        }
        shuffle($this->deck);
    }

    public function dealInitialCards(): void
    {
        $this->resetDeck();
        $this->playerHand = [$this->drawCard(), $this->drawCard()];
        $this->dealerHand = [$this->drawCard(), $this->drawCard()];
        $this->updateScores();
        $this->checkGameOver();
    }

    public function drawCardForPlayer(): void
    {
        if ($this->gameOver) {
            return;
        }

        $this->playerHand[] = $this->drawCard();
        $this->updateScores();
        $this->checkGameOver();
    }

    public function dealerTurn(): void
    {
        if ($this->gameOver) {
            return;
        }

        while ($this->dealerScore < 17) {
            $this->dealerHand[] = $this->drawCard();
            $this->updateScores();
        }

        $this->checkGameOver(true);
    }

    private function drawCard(): string
    {
        return array_pop($this->deck);
    }

    private function updateScores(): void
    {
        $this->playerScore = $this->calculateScore($this->playerHand);
        $this->dealerScore = $this->calculateScore($this->dealerHand);
    }

    private function calculateScore(array $hand): int
    {
        $valueMap = [
            '2' => 2,
            '3' => 3,
            '4' => 4,
            '5' => 5,
            '6' => 6,
            '7' => 7,
            '8' => 8,
            '9' => 9,
            '10' => 10,
            'jack' => 10,
            'queen' => 10,
            'king' => 10,
            'ace' => 14
        ];

        $score = 0;
        $aces = 0;

        foreach ($hand as $card) {
            [$value, $suit] = explode('_of_', $card);
            $score += $valueMap[$value];
            if ($value === 'ace') {
                $aces++;
            }
        }


        while ($score > 21 && $aces > 0) {
            $score -= 13;
            $aces--;
        }

        return $score;
    }

    private function checkGameOver(bool $dealerFinished = false): void
    {
        if ($this->playerScore > 21) {
            $this->result = "Banken vinner! (spelaren över 21)";
            $this->gameOver = true;
        } elseif ($dealerFinished) {
            if ($this->dealerScore > 21) {
                $this->result = "Spelaren vinner! (banken över 21)";
            } elseif ($this->playerScore === $this->dealerScore) {
                $this->result = "Lika! Banken vinner vid lika.";
            } elseif ($this->playerScore > $this->dealerScore) {
                $this->result = "Spelaren vinner!";
            } else {
                $this->result = "Banken vinner!";
            }
            $this->gameOver = true;
        }
    }

    public function getPlayerHand(): array
    {
        return $this->playerHand;
    }

    public function getDealerHand(): array
    {
        return $this->dealerHand;
    }

    public function getPlayerScore(): int
    {
        return $this->playerScore;
    }

    public function getDealerScore(): int
    {
        return $this->dealerScore;
    }

    public function isGameOver(): bool
    {
        return $this->gameOver;
    }

    public function getResult(): ?string
    {
        return $this->result;
    }
}
