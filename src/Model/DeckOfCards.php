<?php

namespace App\Model;

/**
 * DeckOfCards representerar en kortlek bestående av 52 kort.
 * Den innehåller metoder för att dra kort och hämta information om korten i leken.
 */
class DeckOfCards
{
    /** @var Card[] */
    private array $cards = [];

    /**
     * Konstruktor som skapar en ny kortlek och blandar den.
     * Skapar en kortlek med 52 kort (alla kombinationer av 4 färger och 13 ranker).
     * Blandar korten direkt efter skapandet.
     */
    public function __construct()
    {
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
     *
     * Tar bort och returnerar ett kort från kortleken. Om kortleken är tom, returneras null.
     *
     * @return Card|null Det dragna kortet eller null om kortleken är tom.
     */
    public function drawCard(): ?Card
    {
        return array_pop($this->cards);
    }

    /**
     * Hämtar antalet kvarvarande kort i kortleken.
     *
     * @return int Antalet kort som finns kvar i kortleken.
     */
    public function getRemainingCards(): int
    {
        return count($this->cards);
    }

    /**
     * Hämtar alla kort som finns i kortleken.
     *
     * @return Card[] En array med alla kort som finns i kortleken.
     */
    public function getCards(): array
    {
        return $this->cards;
    }
}
