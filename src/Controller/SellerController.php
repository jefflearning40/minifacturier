<?php

namespace App\Controller;

use App\Entity\Seller;
use App\Form\SellerType;
use App\Repository\SellerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/seller')]
final class SellerController extends AbstractController
{
    #[Route(name: 'app_seller_index', methods: ['GET'])]
    public function index(SellerRepository $sellerRepository): Response
    {
        return $this->render('seller/index.html.twig', [
            'sellers' => $sellerRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_seller_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $seller = new Seller();
        $form = $this->createForm(SellerType::class, $seller);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($seller);
            $entityManager->flush();

            return $this->redirectToRoute('app_seller_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('seller/new.html.twig', [
            'seller' => $seller,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_seller_show', methods: ['GET'])]
    public function show(Seller $seller): Response
    {
        return $this->render('seller/show.html.twig', [
            'seller' => $seller,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_seller_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Seller $seller, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SellerType::class, $seller);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_seller_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('seller/edit.html.twig', [
            'seller' => $seller,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_seller_delete', methods: ['POST'])]
    public function delete(Request $request, Seller $seller, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$seller->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($seller);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_seller_index', [], Response::HTTP_SEE_OTHER);
    }
}
