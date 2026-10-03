<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminReviewController extends AbstractController
{
    #[Route('/admin/reviews', name: 'app_reviews')]
    public function index(): Response
    {
        $reviews = [
            [
                'id' => 1,
                'name' => 'Sample Customer',
                'rating' => 5,
                'review_text' => 'Great quality, fits perfectly.',
                'created_at' => new \DateTimeImmutable('2026-09-20'),
            ],
            [
                'id' => 2,
                'name' => 'Another Customer',
                'rating' => 3,
                'review_text' => 'Okay, but shipping was slow.',
                'created_at' => new \DateTimeImmutable('2026-09-28'),
            ],
        ];

        return $this->render('admin_review/index.html.twig', [
            'reviews' => $reviews,
        ]);
    }
}