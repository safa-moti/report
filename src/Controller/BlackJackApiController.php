<?php

namespace App\Controller;

use App\Model\BlackJackGame;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class BlackJackApiController extends AbstractController
{
    private const SESSION_KEY = 'blackjack_game_api';

    #[Route('/api/start/{hands?1}', name: 'api_start', methods: ['GET'])]
    public function start(SessionInterface $session, int $hands = 1): JsonResponse
    {
        $game = new BlackJackGame($hands);
        $session->set(self::SESSION_KEY, serialize($game));

        return $this->json([
            'message' => "Nytt spel startat med $hands hand(er).",
            'playerHands' => $game->getPlayerHands(),
            'dealerHand' => $game->getDealerHand(),
            'deckCount' => $game->getDeckCount(),
        ]);
    }

    #[Route('/api/hit/{handIndex}', name: 'api_hit', methods: ['POST'])]
    public function hit(SessionInterface $session, int $handIndex): JsonResponse
    {
        $game = $this->getGameFromSession($session);

        try {
            $game->hit($handIndex);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }

        $session->set(self::SESSION_KEY, serialize($game));

        return $this->json([
            'message' => "Kort draget på hand $handIndex.",
            'playerHands' => $game->getPlayerHands(),
            'playerScores' => $game->getPlayerScores(),
            'deckCount' => $game->getDeckCount(),
        ]);
    }

    #[Route('/api/stand/{handIndex}', name: 'api_stand', methods: ['POST'])]
    public function stand(SessionInterface $session, int $handIndex): JsonResponse
    {
        $game = $this->getGameFromSession($session);

        try {
            $game->stand($handIndex);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }

        $session->set(self::SESSION_KEY, serialize($game));

        return $this->json([
            'message' => "Hand $handIndex står.",
            'handStatus' => $game->getHandStatus(),
        ]);
    }

    #[Route('/api/dealer-turn', name: 'api_dealer_turn', methods: ['POST'])]
    public function dealerTurn(SessionInterface $session): JsonResponse
    {
        $game = $this->getGameFromSession($session);

        if (in_array('playing', $game->getHandStatus(), true)) {
            return $this->json(['error' => 'Avsluta alla händer innan dealerns tur.'], 400);
        }

        $game->dealerTurn();

        $session->set(self::SESSION_KEY, serialize($game));

        return $this->json([
            'message' => 'Dealerns tur genomförd.',
            'dealerHand' => $game->getDealerHand(),
            'dealerScore' => $game->getDealerScore(),
        ]);
    }

    #[Route('/api/results', name: 'api_results', methods: ['GET'])]
    public function results(SessionInterface $session): JsonResponse
    {
        $game = $this->getGameFromSession($session);

        return $this->json([
            'playerHands' => $game->getPlayerHands(),
            'playerScores' => $game->getPlayerScores(),
            'dealerHand' => $game->getDealerHand(),
            'dealerScore' => $game->getDealerScore(),
            'results' => $game->getResults(),
            'cardsDrawn' => $game->getDrawnCardStats(),
        ]);
    }

    private function getGameFromSession(SessionInterface $session): BlackJackGame
    {
        if (!$session->has(self::SESSION_KEY)) {
            throw $this->createNotFoundException('Spelet har inte startats ännu via API.');
        }

        $game = unserialize($session->get(self::SESSION_KEY), ['allowed_classes' => [BlackJackGame::class]]);

        if (!$game instanceof BlackJackGame) {
            throw $this->createNotFoundException('Spelet är ogiltigt.');
        }

        return $game;
    }
}
