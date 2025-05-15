<?php

namespace App\Card;

class DeckOfCards
{
    /** @var Card[] */
    private array $deck = [];

    /** @var string[] */
    private array $suits = ['♠', '♥', '♦', '♣'];

    /** @var string[] */
    private array $ranks = ['2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K', 'A'];

    public function __construct(bool $includeJokers = false)
    {
        foreach ($this->suits as $suit) {
            foreach ($this->ranks as $rank) {
                $this->deck[] = new Card($suit, $rank);
            }
        }


        if ($includeJokers) {
            $this->deck[] = new Card('', '🃏');
            $this->deck[] = new Card('', '🃏');
        }
    }

    public function shuffleDeck(): void
    {
        shuffle($this->deck);
    }

    /**
     * @return Card[]
     */
    public function getDeck(): array
    {
        return $this->deck;
    }
}
