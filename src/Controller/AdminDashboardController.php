<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminDashboardController extends AbstractController
{
    #[Route('/admin/dashboard', name: 'app_dashboard')]
    public function index(): Response
    {
        return $this->render('admin_dashboard/dashboard.html.twig', [
            'userName' => 'Admin',
            'stats' => [
                'products'  => 0,
                'accounts'  => 0,
                'reviews'   => 0,
                'inventory' => 0,
                'sales'     => 0,
            ],
        ]);
    }
}