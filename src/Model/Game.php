<?php

namespace App\Model;

class Game
{
    private array $deck = [];
    private array $playerHand = [];
    private array $dealerHand = [];

    private int $playerScore = 0;
    private int $dealerScore = 0;

    public function __construct()
    {
        $this->resetDeck();
    }

    private function resetDeck(): void
    {
        $suits = ['clubs', 'diamonds', 'hearts', 'spades'];
        $values = [
            '2',
            '3',
            '4',
            '5',
            '6',
            '7',
            '8',
            '9',
            '10',
            'jack',
            'queen',
            'king',
            'ace'
        ];

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

        $this->playerScore = $this->calculateScore($this->playerHand);
        $this->dealerScore = $this->calculateScore($this->dealerHand);
    }

    public function drawCardForPlayer(): void
    {
        $this->playerHand[] = $this->drawCard();
        $this->playerScore = $this->calculateScore($this->playerHand);
    }

    public function dealerTurn(): void
    {
        while ($this->dealerScore < 17) {
            $this->dealerHand[] = $this->drawCard();
            $this->dealerScore = $this->calculateScore($this->dealerHand);
        }
    }

    private function drawCard(): string
    {
        return array_pop($this->deck);
    }

    /**
     * @param string[] $hand
     */
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
            'ace' => 11,
        ];

        $score = 0;
        $aces = 0;

        foreach ($hand as $card) {
            // card format: value_of_suit
            [$value, $suit] = explode('_of_', $card);
            $score += $valueMap[$value];
            if ($value === 'ace') {
                $aces++;
            }
        }


        while ($score > 21 && $aces > 0) {
            $score -= 10;
            $aces--;
        }

        return $score;
    }

    /**
     * @return string[]
     */
    public function getPlayerHand(): array
    {
        return $this->playerHand;
    }

    /**
     * @return string[]
     */
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
}
