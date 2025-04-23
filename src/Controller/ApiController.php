<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    #[Route('/api', name: 'api_home')]
    public function quote(): JsonResponse
    {
        // Dagens datum och tidsstämpel
        $date = new \DateTime();
        $timestamp = $date->getTimestamp();


        $quotes = [
            "Det är inte den största idén som vinner, utan den som genomförs bäst.",
            "Framgång är att gå från misslyckande till misslyckande utan att förlora entusiasm.",
            "Tålamod är inte bara att vänta, utan att hålla en god attityd medan man väntar."
        ];


        $quote = $quotes[array_rand($quotes)];

        return $this->json([
            'quote' => $quote,
            'date' => $date->format('Y-m-d'),
            'timestamp' => $timestamp
        ]);
    }
}
