<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AdminDashboardController extends AbstractController
{
    #[Route('/admin/dashboard', name: 'app_dashboard')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(
        UserRepository $userRepository,
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository,
    ): Response {
        $users = $userRepository->findAll();
        $staff = array_filter($users, fn ($u) => in_array('ROLE_STAFF', $u->getRoles(), true));

        $lowStock = (int) $productRepository->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->where('p.stock < :limit')
            ->setParameter('limit', 10)
            ->getQuery()
            ->getSingleScalarResult();

        return $this->render('admin_dashboard/dashboard.html.twig', [
            'stats' => [
                'users' => count($users),
                'staff' => count($staff),
                'products' => $productRepository->count([]),
                'categories' => $categoryRepository->count([]),
                'lowStock' => $lowStock,
            ],
        ]);
    }
}