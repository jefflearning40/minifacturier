<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Form\InvoiceType;
use App\Repository\InvoiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensiolabs\GotenbergBundle\GotenbergPdfInterface;
use Sensiolabs\GotenbergBundle\Processor\FileProcessor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/invoice')]
final class InvoiceController extends AbstractController
{
    #[Route('/test-mail', name: 'app_test_mail', methods: ['GET'])]
    public function testMail(MailerInterface $mailer): Response
    {
        $email = (new Email())
            ->from('test@minifacturier.com')
            ->to('client@test.com')
            ->subject('Test Mail')
            ->text('Ceci est un test avec Mailpit');

        $mailer->send($email);

        return new Response('Mail envoyé vers Mailpit');
    }

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
            if ($invoice->getInvoiceItems()->isEmpty()) {
                $this->addFlash('danger', 'Vous devez ajouter au moins une ligne de facture.');

                return $this->redirectToRoute('app_invoice_new');
            }

            foreach ($invoice->getInvoiceItems() as $item) {
                $product = $item->getProduct();
                $quantity = $item->getQuantity();

                if (!$product || $quantity === null || $quantity < 1) {
                    $this->addFlash('danger', 'Chaque ligne doit contenir un produit et une quantité minimum de 1.');

                    return $this->redirectToRoute('app_invoice_new');
                }

                $item->setProductName($product->getName());
                $item->setBrand($product->getBrand());
                $item->setPrice($product->getPrice());
                $item->setVatRate($product->getVatRate());
                $item->setTotal($item->getTotalHt());
            }

            $entityManager->persist($invoice);
            $entityManager->flush();

            return $this->redirectToRoute('app_invoice_index');
        }

        return $this->render('invoice/new.html.twig', [
            'invoice' => $invoice,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/send-mail', name: 'app_invoice_send_mail', methods: ['GET'])]
    public function sendMail(
        Invoice $invoice,
        GotenbergPdfInterface $gotenberg,
        MailerInterface $mailer
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_SELLER');

        $customer = $invoice->getCustomer();
        $clientEmail = $customer?->getAddressCustomer();

        if (!$clientEmail || !filter_var($clientEmail, FILTER_VALIDATE_EMAIL)) {
            $this->addFlash('danger', 'Adresse email client invalide ou manquante.');

            return $this->redirectToRoute('app_invoice_show', [
                'id' => $invoice->getId(),
            ]);
        }

        $filesystem = new Filesystem();
        $pdfDirectory = $this->getParameter('kernel.project_dir') . '/var/pdf';

        if (!$filesystem->exists($pdfDirectory)) {
            $filesystem->mkdir($pdfDirectory);
        }

        $pdfResult = $gotenberg
            ->html()
            ->content('invoice/pdf.html.twig', [
                'invoice' => $invoice,
            ])
            ->processor(new FileProcessor($filesystem, $pdfDirectory))
            ->generate();

        $pdfResult->process();

        $pdfPath = $pdfDirectory . '/' . $pdfResult->getFilename();

        if (!file_exists($pdfPath)) {
            $this->addFlash('danger', 'Le PDF n’a pas pu être généré.');

            return $this->redirectToRoute('app_invoice_show', [
                'id' => $invoice->getId(),
            ]);
        }

        $pdfContent = file_get_contents($pdfPath);

        $email = (new Email())
            ->from('facture@minifacturier.com')
            ->to($clientEmail)
            ->subject('Votre facture ' . $invoice->getNumberInvoice())
            ->text('Bonjour, veuillez trouver votre facture en pièce jointe.')
            ->attach(
                $pdfContent,
                'facture-' . $invoice->getNumberInvoice() . '.pdf',
                'application/pdf'
            );

        $mailer->send($email);

        $this->addFlash('success', 'Facture envoyée avec succès dans Mailpit.');

        return $this->redirectToRoute('app_invoice_show', [
            'id' => $invoice->getId(),
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
            if ($invoice->getInvoiceItems()->isEmpty()) {
                $this->addFlash('danger', 'Vous devez ajouter au moins une ligne de facture.');

                return $this->redirectToRoute('app_invoice_edit', [
                    'id' => $invoice->getId(),
                ]);
            }

            foreach ($invoice->getInvoiceItems() as $item) {
                $product = $item->getProduct();
                $quantity = $item->getQuantity();

                if (!$product || $quantity === null || $quantity < 1) {
                    $this->addFlash('danger', 'Chaque ligne doit contenir un produit et une quantité minimum de 1.');

                    return $this->redirectToRoute('app_invoice_edit', [
                        'id' => $invoice->getId(),
                    ]);
                }

                $item->setProductName($product->getName());
                $item->setBrand($product->getBrand());
                $item->setPrice($product->getPrice());
                $item->setVatRate($product->getVatRate());
                $item->setTotal($item->getTotalHt());
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_invoice_index');
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

        return $this->redirectToRoute('app_invoice_index');
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