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

//    /**
//     * @return Product[] Returns an array of Product objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Product
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

public function search(string $q = '', ?int $categoryId = null, string $status = ''): array
{
    $qb = $this->createQueryBuilder('p')
        ->leftJoin('p.category', 'c')->addSelect('c')
        ->leftJoin('p.createdBy', 'u')->addSelect('u')
        ->orderBy('p.createdAt', 'DESC');

    if ($q !== '') {
        $qb->andWhere('p.name LIKE :q OR p.sku LIKE :q OR u.fullName LIKE :q')
           ->setParameter('q', '%' . $q . '%');
    }

    if ($categoryId) {
        $qb->andWhere('c.id = :cat')->setParameter('cat', $categoryId);
    }

    if ($status === 'active') {
        $qb->andWhere('p.isActive = true');
    } elseif ($status === 'hidden') {
        $qb->andWhere('p.isActive = false');
    } elseif ($status === 'low') {
        $qb->andWhere('p.stock < 10');
    }

    return $qb->getQuery()->getResult();
}
}
