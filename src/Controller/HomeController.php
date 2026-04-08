<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
public function index(): Response
{
    return $this->render('home/index.html.twig');
}
#[Route('/catalogue', name: 'app_catalog_index')]
public function catalogue(): Response
{
    return $this->render('maintenance.html.twig');
}

#[Route('/statistiques', name: 'app_stats_index')]
public function stats(): Response
{
    return $this->render('maintenance.html.twig');
}
}
