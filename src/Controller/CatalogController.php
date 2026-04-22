<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/catalog')]
final class CatalogController extends AbstractController
{
    #[Route('', name: 'app_catalog_index', methods: ['GET'])]
    public function index(
        Request $request,
        ProductRepository $productRepository,
        PaginatorInterface $paginator
    ): Response {
        $search = $request->query->get('search');
        $brand = $request->query->get('brand');

        $queryBuilder = $productRepository->createSearchByNameQueryBuilder($search, $brand);

        $products = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            20
        );

        $brands = $productRepository->findAllBrands();

        return $this->render('catalog/index.html.twig', [
            'products' => $products,
            'search' => $search,
            'brand' => $brand,
            'brands' => $brands,
        ]);
    }
}