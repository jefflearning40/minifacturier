<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/catalog')]
final class CatalogController extends AbstractController
{
    #[Route('', name: 'app_catalog_index', methods: ['GET'])]
public function index(Request $request, ProductRepository $productRepository): Response
{
    $search = $request->query->get('search');

    $products = $productRepository->searchByName($search);

    return $this->render('catalog/index.html.twig', [
        'products' => $products,
        'search' => $search,
    ]);
}
}