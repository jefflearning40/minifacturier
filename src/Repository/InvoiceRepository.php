<?php

namespace App\Repository;

use App\Entity\Invoice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


class InvoiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Invoice::class);
    }

public function countAll(): int
{
    return (int) $this->createQueryBuilder('i')
        ->select('COUNT(i.id)')
        ->getQuery()
        ->getSingleScalarResult();
}

public function countBySeller($seller): int
{
    return (int) $this->createQueryBuilder('i')
        ->select('COUNT(i.id)')
        ->where('i.seller = :seller')
        ->setParameter('seller', $seller)
        ->getQuery()
        ->getSingleScalarResult();
}


}
