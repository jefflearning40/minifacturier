<?php

namespace App\Repository;

use App\Entity\Seller;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SellerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Seller::class);
    }

    public function countAll(): int
    {
        return (int) $this->createQueryBuilder('s')
            ->select('COUNT(s.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getInvoiceCountBySeller(): array
    {
        return $this->createQueryBuilder('s')
            ->leftJoin('s.invoices', 'i')
            ->select('s.id AS id')
            ->addSelect('s.firstNameSeller AS firstName')
            ->addSelect('s.lastNameSeller AS lastName')
            ->addSelect('COUNT(i.id) AS totalInvoices')
            ->groupBy('s.id')
            ->orderBy('totalInvoices', 'DESC')
            ->getQuery()
            ->getResult();
    }
}