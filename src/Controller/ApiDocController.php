<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class ApiDocController extends AbstractController
{
    #[Route('/api', name: 'api_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('api/index.html.twig', [
            'routes' => [
                ['method' => 'GET',  'path' => '/api/game',          'desc' => 'Aktuell ställning för spelet 21 (JSON)'],
                ['method' => 'GET',  'path' => '/api/start/{hands?1}', 'desc' => 'Starta nytt BlackJack-API-spel (JSON)'],
                ['method' => 'POST', 'path' => '/api/hit/{handIndex}', 'desc' => 'Dra kort i BlackJack-API (JSON)'],
                ['method' => 'POST', 'path' => '/api/stand/{handIndex}', 'desc' => 'Stanna hand i BlackJack-API (JSON)'],
                ['method' => 'POST', 'path' => '/api/dealer-turn',   'desc' => 'Dealerns tur i BlackJack-API (JSON)'],
                ['method' => 'GET',  'path' => '/api/results',       'desc' => 'Resultat från BlackJack-API (JSON)'],
                ['method' => 'GET',  'path' => '/api/quote',         'desc' => 'Exempel-endpoint som returnerar dagens citat (JSON)'],
                ['method' => 'GET',  'path' => '/api/deck',          'desc' => 'Visa nuvarande kortlek (JSON)'],
                ['method' => 'GET',  'path' => '/api/deck/shuffle',  'desc' => 'Blanda kortlek (JSON)'],
                ['method' => 'GET',  'path' => '/api/deck/draw',     'desc' => 'Dra ett kort (JSON)'],
                ['method' => 'GET',  'path' => '/api/deck/draw/{n}', 'desc' => 'Dra N kort (JSON)'],
            ],
        ]);
    }
}
