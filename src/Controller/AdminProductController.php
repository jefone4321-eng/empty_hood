<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminProductController extends AbstractController
{
    #[Route('/admin/products', name: 'app_products')]
    public function index(): Response
    {
        $products = [
            [
                'id' => 1,
                'image' => 'images/sample1.jpg',
                'name' => 'Sample Product One',
                'price' => 1299.00,
                'category' => 'Shirts',
                'collection' => 'Summer',
            ],
            [
                'id' => 2,
                'image' => 'images/sample2.jpg',
                'name' => 'Sample Product Two',
                'price' => 899.50,
                'category' => 'Pants',
                'collection' => 'Classic',
            ],
        ];

        return $this->render('admin_product/index.html.twig', [
            'products' => $products,
        ]);
    }
}