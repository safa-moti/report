<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CodeQualityController extends AbstractController
{
    #[Route('/codequality', name: 'code_quality')]
    public function index(): Response
    {
        return $this->render('codequality/index.html.twig');
    }
}
