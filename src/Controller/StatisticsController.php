<?php

namespace App\Controller;

use App\Repository\CustomerRepository;
use App\Repository\InvoiceItemRepository;
use App\Repository\InvoiceRepository;
use App\Repository\SellerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/statistics')]
final class StatisticsController extends AbstractController
{
    #[Route('/admin', name: 'app_statistics_admin', methods: ['GET'])]
    public function admin(
        SellerRepository $sellerRepository,
        CustomerRepository $customerRepository,
        InvoiceRepository $invoiceRepository,
        InvoiceItemRepository $invoiceItemRepository
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $totalSellers = $sellerRepository->countAll();
        $totalCustomers = $customerRepository->countAll();
        $totalInvoices = $invoiceRepository->countAll();

        $totalSalesHt = $invoiceItemRepository->getTotalSalesHt();
        $totalVat = $invoiceItemRepository->getTotalVat();
        $totalSalesTtc = $invoiceItemRepository->getTotalSalesTtc();

        return $this->render('statistics/admin.html.twig', [
            'totalSellers' => $totalSellers,
            'totalCustomers' => $totalCustomers,
            'totalInvoices' => $totalInvoices,
            'totalSalesHt' => $totalSalesHt,
            'totalVat' => $totalVat,
            'totalSalesTtc' => $totalSalesTtc,
        ]);
    }

    #[Route('/seller', name: 'app_statistics_seller', methods: ['GET'])]
    public function seller(
        InvoiceRepository $invoiceRepository,
        InvoiceItemRepository $invoiceItemRepository
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_SELLER');

        $user = $this->getUser();

        if (!$user || !method_exists($user, 'getSeller') || !$user->getSeller()) {
            throw $this->createAccessDeniedException('Aucun vendeur associé à cet utilisateur.');
        }

        $seller = $user->getSeller();

        $totalInvoices = $invoiceRepository->countBySeller($seller);
        $totalSalesHt = $invoiceItemRepository->getSellerTotalSalesHt($seller);
        $totalVat = $invoiceItemRepository->getSellerTotalVat($seller);
        $totalSalesTtc = $invoiceItemRepository->getSellerTotalSalesTtc($seller);

        return $this->render('statistics/seller.html.twig', [
            'seller' => $seller,
            'totalInvoices' => $totalInvoices,
            'totalSalesHt' => $totalSalesHt,
            'totalVat' => $totalVat,
            'totalSalesTtc' => $totalSalesTtc,
        ]);
    }
}