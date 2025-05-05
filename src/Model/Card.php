<?php

namespace App\Model;

class Card
{
    private string $rank;
    private string $suit;

    public function __construct(string $rank, string $suit)
    {
        $this->rank = $rank;
        $this->suit = $suit;
    }

    public function getRank(): string
    {
        return $this->rank;
    }

    public function getSuit(): string
    {
        return $this->suit;
    }

    public function getValue(): int
    {
        if (in_array($this->rank, ['J', 'Q', 'K'])) {
            return 10;
        }
        if ($this->rank == 'A') {
            return 11;
        }
        return (int)$this->rank;
    }

    public function __toString(): string
    {
        return $this->rank . ' of ' . $this->suit;
    }
}
