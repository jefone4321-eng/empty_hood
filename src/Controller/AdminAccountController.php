<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminAccountController extends AbstractController
{
    #[Route('/admin/accounts', name: 'app_accounts')]
    public function index(): Response
    {
        $accounts = [
            [
                'id' => 1,
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'is_admin' => true,
                'created_at' => new \DateTimeImmutable('2026-01-15'),
            ],
            [
                'id' => 2,
                'name' => 'Sample Customer',
                'email' => 'customer@example.com',
                'is_admin' => false,
                'created_at' => new \DateTimeImmutable('2026-03-02'),
            ],
        ];

        return $this->render('admin_account/index.html.twig', [
            'accounts' => $accounts,
            'currentUserId' => 1, // placeholder until login is built
        ]);
    }
}