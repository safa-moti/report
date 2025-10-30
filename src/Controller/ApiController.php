<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    #[Route('/api', name: 'api_home')]
    public function apiHome(): Response
    {
        // Renderar en HTML-sida med översikt över API:et
        return $this->render('api/api_home.html.twig');
    }
}
