<?php

namespace App\Controller;

use App\Model\Game;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class GameApiController extends AbstractController
{
    private const SESSION_KEY = 'game21';

    #[Route("/api/game", name: "api_game")]
    public function gameStatus(SessionInterface $session): JsonResponse
    {
        if (!$session->has(self::SESSION_KEY)) {
            return $this->json([
                'message' => 'Ingen aktiv spelomgång.',
                'player_hand' => [],
                'dealer_hand' => [],
                'player_score' => 0,
                'dealer_score' => 0,
                'deck_remaining' => 52,
                'game_over' => false
            ]);
        }

        /** @var Game $game */
        $game = unserialize($session->get(self::SESSION_KEY), ['allowed_classes' => [Game::class]]);

        return $this->json([
            'player_hand' => $game->getPlayerHand(),
            'dealer_hand' => $game->getDealerHand(),
            'player_score' => $game->getPlayerScore(),
            'dealer_score' => $game->getDealerScore(),
            'result' => $game->getResult(),
            'deck_remaining' => 52 - (count($game->getPlayerHand()) + count($game->getDealerHand())),
        ]);
    }
}
