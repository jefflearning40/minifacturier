<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     *  Recherche + filtre par marque (compatible pagination)
     */
    public function createSearchByNameQueryBuilder(?string $search, ?string $brand)
    {
        $qb = $this->createQueryBuilder('p');

        //  Recherche multi-mots
        if ($search) {
            $words = preg_split('/\s+/', trim($search));

            foreach ($words as $key => $word) {
                if ($word !== '') {
                    $qb->andWhere("LOWER(p.name) LIKE :word$key")
                    ->setParameter("word$key", '%' . strtolower($word) . '%');
                }
            }
        }

        //  Filtre par marque
        if ($brand) {
            $qb->andWhere('p.brand = :brand')
            ->setParameter('brand', $brand);
        }

        //  Tri par défaut
        return $qb
            ->orderBy('p.name', 'ASC')
            ->addOrderBy('p.brand', 'ASC');
    }

    /**
     *  Liste des marques (pour le select)
     */
    public function findAllBrands(): array
    {
        $results = $this->createQueryBuilder('p')
            ->select('DISTINCT p.brand AS brand')
            ->where('p.brand IS NOT NULL')
            ->andWhere('p.brand != :empty')
            ->setParameter('empty', '')
            ->orderBy('p.brand', 'ASC')
            ->getQuery()
            ->getResult();

        return array_column($results, 'brand');
    }
}