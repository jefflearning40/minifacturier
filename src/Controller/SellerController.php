<?php

namespace App\Controller;

use App\Entity\Seller;
use App\Form\SellerType;
use App\Repository\SellerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/seller')]
final class SellerController extends AbstractController
{
    #[Route(name: 'app_seller_index', methods: ['GET'])]
    public function index(
        Request $request,
        SellerRepository $sellerRepository,
        PaginatorInterface $paginator
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $query = $sellerRepository->createQueryBuilder('s')
            ->orderBy('s.id', 'DESC')
            ->getQuery();

        $sellers = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            20
        );

        return $this->render('seller/index.html.twig', [
            'sellers' => $sellers,
        ]);
    }

    #[Route('/new', name: 'app_seller_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $seller = new Seller();
        $form = $this->createForm(SellerType::class, $seller);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($seller);
            $entityManager->flush();

            $this->addFlash('success', 'Vendeur créé avec succès.');

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
        $this->denyAccessUnlessGranted('ROLE_USER');

        return $this->render('seller/show.html.twig', [
            'seller' => $seller,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_seller_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Seller $seller, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(SellerType::class, $seller);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Vendeur modifié avec succès.');

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
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if (!$this->isCsrfTokenValid('delete' . $seller->getId(), $request->getPayload()->getString('_token'))) {
            $this->addFlash('danger', 'Jeton de sécurité invalide. Suppression refusée.');
            return $this->redirectToRoute('app_seller_index', [], Response::HTTP_SEE_OTHER);
        }

        if ($seller->getInvoices()->count() > 0) {
            $this->addFlash('danger', 'Suppression impossible : ce vendeur est lié à une ou plusieurs factures.');
            return $this->redirectToRoute('app_seller_index', [], Response::HTTP_SEE_OTHER);
        }

        $entityManager->remove($seller);
        $entityManager->flush();

        $this->addFlash('success', 'Vendeur supprimé avec succès.');

        return $this->redirectToRoute('app_seller_index', [], Response::HTTP_SEE_OTHER);
    }
}