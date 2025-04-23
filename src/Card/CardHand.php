<?php

namespace App\Card;

class CardHand
{
    private $cards = [];

    public function addCard(Card $card)
    {
        $this->cards[] = $card;
    }

    public function getCards(): array
    {
        return $this->cards;
    }

    public function clearHand()
    {
        $this->cards = [];
    }
}
