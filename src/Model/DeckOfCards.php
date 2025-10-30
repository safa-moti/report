<?php

namespace App\Model;

/**
 * Representerar en kortlek med 52 spelkort.
 */
class DeckOfCards
{
    /** @var Card[] */
    private array $cards = [];

    public function __construct()
    {
        $this->reset();
    }

    /**
     * Skapar en ny blandad kortlek med 52 kort.
     */
    public function reset(): void
    {
        $this->cards = [];

        $suits = ['♠', '♥', '♦', '♣'];
        $ranks = ['2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K', 'A'];

        foreach ($suits as $suit) {
            foreach ($ranks as $rank) {
                $this->cards[] = new Card($rank, $suit);
            }
        }

        shuffle($this->cards);
    }

    /**
     * Drar ett kort från kortleken.
     */
    public function drawCard(): ?Card
    {
        return array_pop($this->cards);
    }

    /**
     * Hämtar antalet kort som finns kvar i kortleken.
     */
    public function getRemainingCards(): int
    {
        return count($this->cards);
    }

    /**
     * Hämtar hela kortleken.
     */
    public function getCards(): array
    {
        return $this->cards;
    }
}
