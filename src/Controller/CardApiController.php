<?php

namespace App\Controller;

use App\Model\DeckOfCards;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CardApiController extends AbstractController
{
    /**
     * Returnerar hela kortleken i JSON-format.
     */
    #[Route('/api/deck', name: 'api_deck', methods: ['GET'])]
    public function getDeck(SessionInterface $session): JsonResponse
    {
        $deck = $session->get('deck');


        if (!$deck instanceof DeckOfCards) {
            $deck = new DeckOfCards();
            $session->set('deck', $deck);
        }

        return $this->json([
            'deck' => array_map(fn($card) => (string)$card, $deck->getCards()),
            'remaining' => $deck->getRemainingCards()
        ]);
    }

    /**
     * Blandar en ny kortlek.
     */
    #[Route('/api/deck/shuffle', name: 'api_deck_shuffle', methods: ['GET'])]
    public function shuffleDeck(SessionInterface $session): JsonResponse
    {
        $deck = new DeckOfCards();
        $session->set('deck', $deck);

        return $this->json([
            'message' => 'Kortleken har blandats.',
            'remaining' => $deck->getRemainingCards()
        ]);
    }

    /**
     * Drar ett kort från kortleken.
     */
    #[Route('/api/deck/draw', name: 'api_deck_draw', methods: ['GET'])]
    public function drawCard(SessionInterface $session): JsonResponse
    {
        $deck = $session->get('deck');

        if (!$deck instanceof DeckOfCards) {
            $deck = new DeckOfCards();
        }

        $card = $deck->drawCard();
        $session->set('deck', $deck);

        return $this->json([
            'card' => (string)$card,
            'remaining' => $deck->getRemainingCards()
        ]);
    }

    /**
     * Drar flera kort från kortleken.
     */
    #[Route('/api/deck/draw/{number}', name: 'api_deck_draw_number', methods: ['GET'])]
    public function drawMultipleCards(int $number, SessionInterface $session): JsonResponse
    {
        $deck = $session->get('deck');

        if (!$deck instanceof DeckOfCards) {
            $deck = new DeckOfCards();
        }

        $cards = [];
        for ($i = 0; $i < $number; $i++) {
            $card = $deck->drawCard();
            if ($card === null) {
                break;
            }
            $cards[] = (string)$card;
        }

        $session->set('deck', $deck);

        return $this->json([
            'cards' => $cards,
            'remaining' => $deck->getRemainingCards()
        ]);
    }
}
