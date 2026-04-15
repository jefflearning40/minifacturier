<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/catalog')]
final class CatalogController extends AbstractController
{
    #[Route('', name: 'app_catalog_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findBy([], [
            'name' => 'ASC',
            'brand' => 'ASC',
        ]);

        return $this->render('catalog/index.html.twig', [
            'products' => $products,
        ]);
    }
}