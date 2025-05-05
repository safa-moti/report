<?php

namespace App\Controller;

use App\Model\Game;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class GameController extends AbstractController
{
    private Game $game;

    public function __construct()
    {
        $this->game = new Game();
    }

    #[Route("/game", name: "game_home")]
    public function index(): Response
    {
        return $this->render('game/index.html.twig');
    }

    #[Route("/game/doc", name: "game_doc")]
    public function documentation(): Response
    {
        return $this->render('game/doc.html.twig');
    }

    #[Route("/game/play", name: "game_play")]
    public function play(): Response
    {
        $this->game->dealInitialCards();

        return $this->render('game/play.html.twig', [
            'player_hand' => $this->game->getPlayerHand(),
            'dealer_hand' => $this->game->getDealerHand(),
            'player_score' => $this->game->getPlayerScore(),
            'dealer_score' => $this->game->getDealerScore()
        ]);
    }

    #[Route("/game/draw", name: "game_draw")]
    public function draw(): Response
    {
        $this->game->drawCardForPlayer();

        return $this->redirectToRoute('game_play');
    }

    #[Route("/game/dealer_turn", name: "game_dealer_turn")]
    public function dealerTurn(): Response
    {
        $this->game->dealerTurn();

        return $this->redirectToRoute('game_play');
    }
}
