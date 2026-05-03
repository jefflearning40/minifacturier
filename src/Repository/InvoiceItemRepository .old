<?php

namespace App\Repository;

use App\Entity\InvoiceItem;
use App\Entity\Seller;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class InvoiceItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InvoiceItem::class);
    }

    public function getTotalSalesHt(): float
    {
        $result = $this->createQueryBuilder('ii')
            ->select('SUM(ii.price * ii.quantity) as total')
            ->getQuery()
            ->getSingleScalarResult();

        return (float) ($result ?? 0);
    }

    public function getTotalVat(): float
    {
        $result = $this->createQueryBuilder('ii')
            ->select('SUM((ii.price * ii.quantity) * (ii.vatRate / 100)) as total')
            ->getQuery()
            ->getSingleScalarResult();

        return (float) ($result ?? 0);
    }

    public function getTotalSalesTtc(): float
    {
        $result = $this->createQueryBuilder('ii')
            ->select('SUM((ii.price * ii.quantity) + ((ii.price * ii.quantity) * (ii.vatRate / 100))) as total')
            ->getQuery()
            ->getSingleScalarResult();

        return (float) ($result ?? 0);
    }

    public function getSellerTotalSalesHt(Seller $seller): float
    {
        $result = $this->createQueryBuilder('ii')
            ->join('ii.invoice', 'i')
            ->select('SUM(ii.price * ii.quantity) as total')
            ->where('i.seller = :seller')
            ->setParameter('seller', $seller)
            ->getQuery()
            ->getSingleScalarResult();

        return (float) ($result ?? 0);
    }

    public function getSellerTotalVat(Seller $seller): float
    {
        $result = $this->createQueryBuilder('ii')
            ->join('ii.invoice', 'i')
            ->select('SUM((ii.price * ii.quantity) * (ii.vatRate / 100)) as total')
            ->where('i.seller = :seller')
            ->setParameter('seller', $seller)
            ->getQuery()
            ->getSingleScalarResult();

        return (float) ($result ?? 0);
    }

    public function getSellerTotalSalesTtc(Seller $seller): float
    {
        $result = $this->createQueryBuilder('ii')
            ->join('ii.invoice', 'i')
            ->select('SUM((ii.price * ii.quantity) + ((ii.price * ii.quantity) * (ii.vatRate / 100))) as total')
            ->where('i.seller = :seller')
            ->setParameter('seller', $seller)
            ->getQuery()
            ->getSingleScalarResult();

        return (float) ($result ?? 0);
    }

    public function getSellerTotalProductsSold(Seller $seller): int
    {
        $result = $this->createQueryBuilder('ii')
            ->join('ii.invoice', 'i')
            ->select('SUM(ii.quantity) as total')
            ->where('i.seller = :seller')
            ->setParameter('seller', $seller)
            ->getQuery()
            ->getSingleScalarResult();

        return (int) ($result ?? 0);
    }

    public function getMostSoldProduct(): ?array
    {
        return $this->createQueryBuilder('ii')
            ->select('ii.productName AS productName, ii.brand AS brand, SUM(ii.quantity) AS totalQuantity')
            ->groupBy('ii.productName, ii.brand')
            ->orderBy('totalQuantity', 'DESC')
            ->addOrderBy('ii.productName', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getLeastSoldProduct(): ?array
    {
        return $this->createQueryBuilder('ii')
            ->select('ii.productName AS productName, ii.brand AS brand, SUM(ii.quantity) AS totalQuantity')
            ->groupBy('ii.productName, ii.brand')
            ->orderBy('totalQuantity', 'ASC')
            ->addOrderBy('ii.productName', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getSellerSalesByProduct(Seller $seller): array
    {
        return $this->createQueryBuilder('ii')
            ->join('ii.invoice', 'i')
            ->select('ii.productName AS productName, ii.brand AS brand, SUM(ii.quantity) AS totalQuantity')
            ->where('i.seller = :seller')
            ->setParameter('seller', $seller)
            ->groupBy('ii.productName, ii.brand')
            ->orderBy('totalQuantity', 'DESC')
            ->addOrderBy('ii.productName', 'ASC')
            ->getQuery()
            ->getResult();
    }
    public function getSalesByProduct(): array
{
    return $this->createQueryBuilder('ii')
        ->select('ii.productName AS productName, ii.brand AS brand, SUM(ii.quantity) AS totalQuantity')
        ->groupBy('ii.productName, ii.brand')
        ->orderBy('totalQuantity', 'DESC')
        ->addOrderBy('ii.productName', 'ASC')
        ->getQuery()
        ->getResult();
}
}