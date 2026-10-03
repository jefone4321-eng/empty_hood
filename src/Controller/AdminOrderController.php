<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminOrderController extends AbstractController
{
    #[Route('/admin/orders', name: 'app_orders')]
    public function index(Request $request): Response
    {
        $search = trim((string) $request->query->get('q', ''));

        $stats = [
            'totalRevenue' => 0,
            'todayRevenue' => 0,
            'totalOrders' => 0,
            'pendingCount' => 0,
            'awaitingVerification' => 0,
        ];

        $orders = [
            [
                'id' => 1,
                'transaction_number' => 'TXN-0001',
                'customer_name' => 'Sample Customer',
                'customer_email' => 'customer@example.com',
                'created_at' => new \DateTimeImmutable('2026-09-30'),
                'payment_method' => 'GCash',
                'reference_number' => '123456789',
                'payment_status' => 'Pending Verification',
                'total' => 1299.00,
                'status' => 'Pending',
            ],
            [
                'id' => 2,
                'transaction_number' => null,
                'customer_name' => 'Another Customer',
                'customer_email' => 'another@example.com',
                'created_at' => new \DateTimeImmutable('2026-10-01'),
                'payment_method' => 'Cash on Delivery',
                'reference_number' => null,
                'payment_status' => 'Paid',
                'total' => 899.50,
                'status' => 'Cancelled',
            ],
        ];

        return $this->render('admin_order/index.html.twig', [
            'stats' => $stats,
            'orders' => $orders,
            'search' => $search,
            'statusOptions' => ['Pending', 'Processing', 'Shipped', 'Delivered'],
            'paymentStatusOptions' => ['Unpaid', 'Pending Verification', 'Paid'],
        ]);
    }
}