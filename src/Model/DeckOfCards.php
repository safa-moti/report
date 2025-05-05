<?php

namespace App\Model;

class DeckOfCards
{
    /** @var Card[] */
    private array $cards = [];

    public function __construct()
    {
        $suits = ['Hearts', 'Diamonds', 'Clubs', 'Spades'];
        $ranks = ['2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K', 'A'];

        foreach ($suits as $suit) {
            foreach ($ranks as $rank) {
                $this->cards[] = new Card($rank, $suit);
            }
        }
        shuffle($this->cards);
    }

    public function drawCard(): ?Card
    {
        return array_pop($this->cards);
    }

    public function getRemainingCards(): int
    {
        return count($this->cards);
    }
}
