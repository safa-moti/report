<?php

namespace App\Controller;

use App\Model\BlackJackGame;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProjectController extends AbstractController
{
    private const SESSION_KEY = 'blackjack_game';

    #[Route('/proj', name: 'proj_home')]
    public function index(): Response
    {
        return $this->render('proj/index.html.twig');
    }

    #[Route('/proj/about', name: 'proj_about')]
    public function about(): Response
    {
        return $this->render('proj/about.html.twig');
    }

    #[Route('/proj/start', name: 'proj_start')]
    public function start(Request $request): Response
    {
        $handCount = (int) $request->query->get('hands', 1);
        if ($handCount < 1 || $handCount > 3) {
            $handCount = 1;
        }

        $game = new BlackJackGame($handCount);

        $session = $request->getSession();
        $session->set(self::SESSION_KEY, serialize($game));

        return $this->redirectToRoute('proj_play');
    }

    private function getGame(Request $request): ?BlackJackGame
    {
        $session = $request->getSession();
        if (!$session->has(self::SESSION_KEY)) {
            return null;
        }
        return unserialize($session->get(self::SESSION_KEY));
    }

    private function saveGame(Request $request, BlackJackGame $game): void
    {
        $session = $request->getSession();
        $session->set(self::SESSION_KEY, serialize($game));
    }

    #[Route('/proj/play', name: 'proj_play')]
    public function play(Request $request): Response
    {
        $game = $this->getGame($request);
        if (!$game) {
            return $this->redirectToRoute('proj_home');
        }

        return $this->render('proj/play.html.twig', [
            'playerHands' => $game->getPlayerHands(),
            'playerScores' => $game->getPlayerScores(),
            'handStatus' => $game->getHandStatus(),
            'dealerHand' => $game->getDealerHand(),
            'dealerScore' => $game->getDealerScore(),
            'deckCount' => $game->getDeckCount(),
            'cardsDrawn' => $game->getCardsDrawn(),
        ]);
    }

    #[Route('/proj/hit/{handIndex}', name: 'proj_hit')]
    public function hit(Request $request, int $handIndex): Response
    {
        $game = $this->getGame($request);
        if (!$game) {
            return $this->redirectToRoute('proj_home');
        }

        $game->hit($handIndex);
        $this->saveGame($request, $game);

        return $this->redirectToRoute('proj_play');
    }

    #[Route('/proj/stand/{handIndex}', name: 'proj_stand')]
    public function stand(Request $request, int $handIndex): Response
    {
        $game = $this->getGame($request);
        if (!$game) {
            return $this->redirectToRoute('proj_home');
        }

        $game->stand($handIndex);
        $this->saveGame($request, $game);

        return $this->redirectToRoute('proj_play');
    }

    #[Route('/proj/dealer-turn', name: 'proj_dealer_turn')]
    public function dealerTurn(Request $request): Response
    {
        $game = $this->getGame($request);
        if (!$game) {
            return $this->redirectToRoute('proj_home');
        }

        if (!$game->allHandsFinished()) {
            $this->addFlash('error', 'Du måste avsluta alla händer innan dealerns tur.');
            return $this->redirectToRoute('proj_play');
        }

        $game->dealerTurn();
        $this->saveGame($request, $game);

        return $this->redirectToRoute('proj_results');
    }

    #[Route('/proj/results', name: 'proj_results')]
    public function results(Request $request): Response
    {
        $game = $this->getGame($request);
        if (!$game) {
            return $this->redirectToRoute('proj_home');
        }

        $results = $game->getResults();

        return $this->render('proj/results.html.twig', [
            'playerHands' => $game->getPlayerHands(),
            'playerScores' => $game->getPlayerScores(),
            'dealerHand' => $game->getDealerHand(),
            'dealerScore' => $game->getDealerScore(),
            'results' => $results,
            'cardsDrawn' => $game->getCardsDrawn(),
        ]);
    }
    #[Route('/proj/api', name: 'proj_api', methods: ['GET'])]
    public function apiTestPage()
    {
        return $this->render('proj/api.html.twig');
    }
}
