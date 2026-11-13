<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

//    /**
//     * @return Category[] Returns an array of Category objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Category
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

#[Route('', name: 'app_product_index', methods: ['GET'])]
public function index(Request $request, ProductRepository $productRepository, CategoryRepository $categoryRepository): Response
{
    $q = trim((string) $request->query->get('q', ''));
    $category = $request->query->get('category');
    $status = (string) $request->query->get('status', '');

    return $this->render('product/index.html.twig', [
        'products' => $productRepository->search($q, $category ? (int) $category : null, $status),
        'categories' => $categoryRepository->findAll(),
        'q' => $q,
        'selectedCategory' => $category ? (int) $category : null,
        'status' => $status,
    ]);
}
}
