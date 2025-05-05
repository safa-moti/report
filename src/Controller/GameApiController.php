<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class GameApiController extends AbstractController
{
    #[Route("/api/game", name: "api_game")]
    public function gameStatus(): JsonResponse
    {
        // Här hämtar du spelets nuvarande status, t.ex. spelarens och bankens poäng
        $gameStatus = [
            'player_score' => 18,
            'dealer_score' => 17,
            'deck_remaining' => 52,
            'game_over' => false
        ];

        return new JsonResponse($gameStatus);
    }
}
