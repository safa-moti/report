<?php

namespace App\Controller;

use App\Card\DeckOfCards;
use App\Card\CardHand;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CardController extends AbstractController
{
    #[Route('/card', name: 'card_home')]
    public function home(): Response
    {
        return $this->render('card/home.html.twig');
    }

    #[Route('/card/deck', name: 'card_deck')]
    public function viewDeck(SessionInterface $session): Response
    {
        // Hämta kortleken från sessionen
        $deck = $session->get('deck');

        // Om ingen kortlek finns i sessionen (eller om den är tom), skapa en ny kortlek
        if (!$deck || $deck->getRemainingCards() === 0) {
            $deck = new DeckOfCards();
            $session->set('deck', $deck);
        }

        // Hämta alla kort från kortleken
        $cards = $deck->getDeck();

        return $this->render('card/deck.html.twig', [
            'cards' => $cards
        ]);
    }


    #[Route('/card/deck/shuffle', name: 'card_deck_shuffle')]
    public function shuffleDeck(SessionInterface $session): Response
    {
        $deck = $session->get('deck');

        if (!$deck) {
            $deck = new DeckOfCards();
            $session->set('deck', $deck);
        }

        $deck->shuffleDeck();
        $session->set('deck', $deck);

        return $this->redirectToRoute('card_deck');
    }


    #[Route('/card/deck/draw', name: 'card_deck_draw')]
    public function drawCard(SessionInterface $session): Response
    {
        $deck = $session->get('deck');
        $card = $deck->drawCard();
        $session->set('deck', $deck);

        return $this->render('card/draw.html.twig', [
            'card' => $card,
            'remaining' => $deck->getRemainingCards()
        ]);
    }

    #[Route('/card/deck/draw/{number}', name: 'card_deck_draw_number')]
    public function drawMultipleCards(int $number, SessionInterface $session): Response
    {
        $deck = $session->get('deck');
        $cards = [];
        for ($i = 0; $i < $number; $i++) {
            $cards[] = $deck->drawCard();
        }
        $session->set('deck', $deck);

        return $this->render('card/draw_multiple.html.twig', [
            'cards' => $cards,
            'remaining' => $deck->getRemainingCards()
        ]);
    }

    #[Route('/card/deck/delete', name: 'card_deck_delete')]
    public function deleteDeck(SessionInterface $session): Response
    {
        $session->remove('deck');
        $this->addFlash('notice', 'Deck deleted successfully!');

        // Skapa en ny kortlek om den har raderats
        $deck = new DeckOfCards();
        $session->set('deck', $deck);

        return $this->redirectToRoute('card_deck');
    }

    #[Route('/card/deck/deal/{players}/{cards}', name: 'card_deck_deal')]
    public function dealCards(int $players, int $cards, SessionInterface $session): Response
    {
        $deck = $session->get('deck');
        $dealtCards = [];

        // Här delar vi korten till spelare
        for ($i = 0; $i < $players; $i++) {
            $playerHand = [];
            for ($j = 0; $j < $cards; $j++) {
                $playerHand[] = $deck->drawCard();
            }
            $dealtCards[] = $playerHand;
        }

        // Spara tillbaka kortleken om den inte är tom
        $session->set('deck', $deck);

        return $this->render('card/deal.html.twig', [
            'dealtCards' => $dealtCards,
            'remaining' => $deck->getRemainingCards(),
        ]);
    }
}
