<?php

namespace App\Card;

class DeckOfCards
{
    private $deck = [];
    private $suits = ['♠', '♥', '♦', '♣'];
    private $ranks = ['2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K', 'A'];

    public function __construct(bool $includeJokers = false)
    {
        foreach ($this->suits as $suit) {
            foreach ($this->ranks as $rank) {
                $this->deck[] = new Card($suit, $rank);
            }
        }
    }

    public function shuffleDeck()
    {
        shuffle($this->deck);
    }

    public function drawCard(): ?Card
    {
        return array_pop($this->deck);
    }

    public function getDeck(): array
    {
        return $this->deck;
    }

    public function getRemainingCards(): int
    {
        return count($this->deck);
    }
}
