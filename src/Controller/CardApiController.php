<?php

namespace App\Controller;

use App\Card\DeckOfCards;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CardApiController extends AbstractController
{
    #[Route('/api/deck', name: 'api_deck')]
    public function getDeck(SessionInterface $session): JsonResponse
    {
        $deck = $session->get('deck');
        return new JsonResponse($deck->getDeck());
    }

    #[Route('/api/deck/shuffle', name: 'api_deck_shuffle')]
    public function shuffleDeck(SessionInterface $session): JsonResponse
    {
        $deck = $session->get('deck');
        $deck->shuffleDeck();
        $session->set('deck', $deck);
        return new JsonResponse($deck->getDeck());
    }

    #[Route('/api/deck/draw', name: 'api_deck_draw')]
    public function drawCard(SessionInterface $session): JsonResponse
    {
        $deck = $session->get('deck');
        $card = $deck->drawCard();
        $session->set('deck', $deck);
        return new JsonResponse(['card' => (string)$card, 'remaining' => $deck->getRemainingCards()]);
    }

    #[Route('/api/deck/draw/{number}', name: 'api_deck_draw_number')]
    public function drawMultipleCards(int $number, SessionInterface $session): JsonResponse
    {
        $deck = $session->get('deck');
        $cards = [];
        for ($i = 0; $i < $number; $i++) {
            $cards[] = (string)$deck->drawCard();
        }
        $session->set('deck', $deck);
        return new JsonResponse(['cards' => $cards, 'remaining' => $deck->getRemainingCards()]);
    }
}
