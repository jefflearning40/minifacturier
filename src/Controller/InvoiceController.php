<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Form\InvoiceType;
use App\Repository\InvoiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensiolabs\GotenbergBundle\GotenbergPdfInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/invoice')]
final class InvoiceController extends AbstractController
{
    #[Route(name: 'app_invoice_index', methods: ['GET'])]
    public function index(
        Request $request,
        InvoiceRepository $invoiceRepository,
        PaginatorInterface $paginator
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $query = $invoiceRepository->createQueryBuilder('i')
            ->orderBy('i.id', 'DESC')
            ->getQuery();

        $invoices = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            20
        );

        return $this->render('invoice/index.html.twig', [
            'invoices' => $invoices,
        ]);
    }

    #[Route('/new', name: 'app_invoice_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SELLER');

        $invoice = new Invoice();
        $form = $this->createForm(InvoiceType::class, $invoice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($invoice->getInvoiceItems() as $item) {
                $product = $item->getProduct();

                if (!$product) {
                    continue;
                }

                $item->setProductName($product->getName());
                $item->setBrand($product->getBrand());
                $item->setPrice($product->getPrice());
                $item->setVatRate($product->getVatRate());
                $item->setTotal($item->getTotalHt());
            }

            $entityManager->persist($invoice);
            $entityManager->flush();

            return $this->redirectToRoute('app_invoice_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('invoice/new.html.twig', [
            'invoice' => $invoice,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_invoice_show', methods: ['GET'])]
    public function show(Invoice $invoice): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        return $this->render('invoice/show.html.twig', [
            'invoice' => $invoice,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_invoice_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Invoice $invoice, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SELLER');

        $form = $this->createForm(InvoiceType::class, $invoice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($invoice->getInvoiceItems() as $item) {
                $product = $item->getProduct();

                if (!$product) {
                    continue;
                }

                $item->setProductName($product->getName());
                $item->setBrand($product->getBrand());
                $item->setPrice($product->getPrice());
                $item->setVatRate($product->getVatRate());
                $item->setTotal($item->getTotalHt());
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_invoice_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('invoice/edit.html.twig', [
            'invoice' => $invoice,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_invoice_delete', methods: ['POST'])]
    public function delete(Request $request, Invoice $invoice, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SELLER');

        if ($this->isCsrfTokenValid('delete' . $invoice->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($invoice);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_invoice_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/pdf', name: 'app_invoice_pdf', methods: ['GET'])]
    public function pdf(Invoice $invoice, GotenbergPdfInterface $gotenberg): Response
    {
        return $gotenberg
            ->html()
            ->content('invoice/pdf.html.twig', [
                'invoice' => $invoice,
            ])
            ->generate()
            ->stream();
    }
}