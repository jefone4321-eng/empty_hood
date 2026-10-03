<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminInventoryController extends AbstractController
{
    #[Route('/admin/inventory', name: 'app_inventory')]
    public function index(): Response
    {
        $products = [
            ['id' => 1, 'name' => 'Sample Product One', 'image' => 'images/sample1.jpg', 'stock' => 0],
            ['id' => 2, 'name' => 'Sample Product Two', 'image' => 'images/sample2.jpg', 'stock' => 3],
            ['id' => 3, 'name' => 'Sample Product Three', 'image' => 'images/sample3.jpg', 'stock' => 24],
        ];

        return $this->render('admin_inventory/index.html.twig', [
            'products' => $products,
            'message' => null, // later: set after a successful save
        ]);
    }
}