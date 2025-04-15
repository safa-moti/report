<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    #[Route('/api/quote', name: 'api_quote')]
    public function quote(): JsonResponse
    {
        $quotes = [
            "The only limit to our realization of tomorrow is our doubts of today.",
            "Code is like humor. When you have to explain it, it's bad.",
            "In the middle of difficulty lies opportunity. – Einstein"
        ];

        $randomQuote = $quotes[array_rand($quotes)];

        return $this->json([
            'quote' => $randomQuote,
            'date' => (new \DateTime())->format('Y-m-d'),
            'timestamp' => (new \DateTime())->format(\DateTime::ATOM),
        ]);
    }

    #[Route('/api/', name: 'api_index')]
    public function index(): Response
    {
        return $this->render('api/index.html.twig', [
            'routes' => [
                [
                    'path' => '/api/quote',
                    'description' => 'Ger ett slumpmässigt citat som JSON med datum och tidsstämpel.'
                ]

            ]
        ]);
    }
}
