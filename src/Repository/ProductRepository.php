<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }
    public function searchByName(?string $search): array
    {
    $qb = $this->createQueryBuilder('p');

    if ($search) {
        $qb->where('LOWER(p.name) LIKE LOWER(:search)')
        ->setParameter('search', '%' . $search . '%');
    }

    return $qb
        ->orderBy('p.name', 'ASC')
        ->addOrderBy('p.brand', 'ASC')
        ->getQuery()
        ->getResult();
}
    
}
