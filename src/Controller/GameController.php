<?php

namespace App\Controller;

use App\Model\Game;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class GameController extends AbstractController
{
    private const SESSION_KEY = 'game21';

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

    #[Route("/game/new", name: "game_new")]
    public function new(SessionInterface $session): Response
    {
        $game = new Game();
        $game->dealInitialCards();
        $session->set(self::SESSION_KEY, serialize($game));

        return $this->redirectToRoute('game_play');
    }

    #[Route("/game/play", name: "game_play")]
    public function play(SessionInterface $session): Response
    {
        if (!$session->has(self::SESSION_KEY)) {
            return $this->redirectToRoute('game_new');
        }

        /** @var Game $game */
        $game = unserialize($session->get(self::SESSION_KEY), ['allowed_classes' => [Game::class]]);

        return $this->render('game/play.html.twig', [
            'player_hand' => $game->getPlayerHand(),
            'dealer_hand' => $game->getDealerHand(),
            'player_score' => $game->getPlayerScore(),
            'dealer_score' => $game->getDealerScore(),
            'result' => $game->getResult(),
            'game_over' => $game->isGameOver(),
        ]);
    }

    #[Route("/game/draw", name: "game_draw")]
    public function draw(SessionInterface $session): Response
    {
        /** @var Game $game */
        $game = unserialize($session->get(self::SESSION_KEY), ['allowed_classes' => [Game::class]]);

        if (!$game->isGameOver()) {
            $game->drawCardForPlayer();
        }

        $session->set(self::SESSION_KEY, serialize($game));
        return $this->redirectToRoute('game_play');
    }

    #[Route("/game/stand", name: "game_stand")]
    public function stand(SessionInterface $session): Response
    {
        /** @var Game $game */
        $game = unserialize($session->get(self::SESSION_KEY), ['allowed_classes' => [Game::class]]);

        if (!$game->isGameOver()) {
            $game->dealerTurn();
        }

        $session->set(self::SESSION_KEY, serialize($game));
        return $this->redirectToRoute('game_play');
    }

    #[Route("/game/reset", name: "game_reset")]
    public function reset(SessionInterface $session): Response
    {
        $session->remove(self::SESSION_KEY);
        return $this->redirectToRoute('game_home');
    }
}
