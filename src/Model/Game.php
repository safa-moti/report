<?php

namespace App\Model;

class Game
{
    /** @var int[] */
    private array $playerHand = [];

    /** @var int[] */
    private array $dealerHand = [];

    private int $playerScore = 0;
    private int $dealerScore = 0;

    public function dealInitialCards(): void
    {
        $this->playerHand = [$this->drawCard(), $this->drawCard()];
        $this->dealerHand = [$this->drawCard(), $this->drawCard()];

        $this->playerScore = $this->calculateScore($this->playerHand);
        $this->dealerScore = $this->calculateScore($this->dealerHand);
    }

    public function drawCardForPlayer(): void
    {
        $card = $this->drawCard();
        $this->playerHand[] = $card;
        $this->playerScore = $this->calculateScore($this->playerHand);
    }

    public function dealerTurn(): void
    {
        while ($this->dealerScore < 17) {
            $card = $this->drawCard();
            $this->dealerHand[] = $card;
            $this->dealerScore = $this->calculateScore($this->dealerHand);
        }
    }

    private function drawCard(): int
    {
        return rand(1, 11);
    }

    /**
     * @param int[] $hand
     */
    private function calculateScore(array $hand): int
    {
        return array_sum($hand);
    }

    /**
     * @return int[]
     */
    public function getPlayerHand(): array
    {
        return $this->playerHand;
    }

    /**
     * @return int[]
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
