<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
//use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Annotation\Route;

class ErrorController extends AbstractController
{
    #[Route('/error404', name: 'app_error404')]
    public function error404(): Response
    {
        return $this->render('bundles/TwigBundle/Exeption/error404.html.twig');
    }

    #[Route('/error403', name: 'app_error403')]
    public function error403(): Response
    {
        return $this->render('bundles/TwigBundle/Exeption/error403.html.twig');
    }
}