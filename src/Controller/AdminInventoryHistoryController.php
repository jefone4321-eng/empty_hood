<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminInventoryHistoryController extends AbstractController
{
    #[Route('/admin/inventory-history', name: 'app_inventory_history')]
    public function index(Request $request): Response
    {
        $typeLabels = [
            'cart_reserve'      => 'Added to Bag',
            'cart_release'      => 'Removed from Bag',
            'sale'              => 'Sale (Buy Now)',
            'restock_cancel'    => 'Order Cancelled',
            'manual_adjustment' => 'Manual Edit',
        ];

        $typeColors = [
            'cart_reserve'      => 'background:rgba(140,158,255,0.15); color:#8c9eff;',
            'cart_release'      => 'background:rgba(140,158,255,0.15); color:#8c9eff;',
            'sale'              => 'background:rgba(192,57,43,0.18); color:#e08080;',
            'restock_cancel'    => 'background:rgba(92,140,87,0.18); color:#8fc98a;',
            'manual_adjustment' => 'background:rgba(201,162,39,0.18); color:#d9b84a;',
        ];

        $filterProductId = $request->query->get('product_id') !== null && $request->query->get('product_id') !== ''
            ? (int) $request->query->get('product_id')
            : null;

        $filterType = $request->query->get('change_type');
        if (!array_key_exists((string) $filterType, $typeLabels)) {
            $filterType = null;
        }

        $page = max(1, (int) $request->query->get('page', 1));

        $products = [
            ['id' => 1, 'name' => 'Sample Product One'],
            ['id' => 2, 'name' => 'Sample Product Two'],
        ];

        $logs = [
            [
                'created_at' => new \DateTimeImmutable('2026-10-01 14:30'),
                'product_name' => 'Sample Product One',
                'product_image' => 'images/sample1.jpg',
                'change_type' => 'manual_adjustment',
                'quantity_change' => 10,
                'previous_stock' => 0,
                'new_stock' => 10,
                'reference_id' => null,
                'note' => 'Manual stock edit by admin',
            ],
            [
                'created_at' => new \DateTimeImmutable('2026-10-02 09:15'),
                'product_name' => 'Sample Product Two',
                'product_image' => 'images/sample2.jpg',
                'change_type' => 'sale',
                'quantity_change' => -2,
                'previous_stock' => 5,
                'new_stock' => 3,
                'reference_id' => 14,
                'note' => '',
            ],
            [
                'created_at' => new \DateTimeImmutable('2026-10-02 18:40'),
                'product_name' => 'Sample Product Two',
                'product_image' => 'images/sample2.jpg',
                'change_type' => 'restock_cancel',
                'quantity_change' => 2,
                'previous_stock' => 3,
                'new_stock' => 5,
                'reference_id' => 14,
                'note' => 'Order cancelled by admin',
            ],
        ];

        return $this->render('admin_inventory_history/index.html.twig', [
            'logs' => $logs,
            'products' => $products,
            'typeLabels' => $typeLabels,
            'typeColors' => $typeColors,
            'filterProductId' => $filterProductId,
            'filterType' => $filterType,
            'page' => $page,
            'totalPages' => 3, // sample value so you can preview the pagination
        ]);
    }
}