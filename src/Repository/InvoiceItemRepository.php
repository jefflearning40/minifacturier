<?php

namespace App\Repository;

use App\Entity\InvoiceItem;
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

public function getSellerTotalSalesHt($seller): float
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

public function getSellerTotalVat($seller): float
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

public function getSellerTotalSalesTtc($seller): float
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
}
