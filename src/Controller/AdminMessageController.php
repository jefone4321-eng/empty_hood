<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminMessageController extends AbstractController
{
    #[Route('/admin/messages', name: 'app_messages')]
    public function index(): Response
    {
        $messages = [
            [
                'id' => 1,
                'name' => 'Sample Customer',
                'email' => 'customer@example.com',
                'message' => "Hi, do you have this in a larger size?\nThanks!",
                'submitted_at' => new \DateTimeImmutable('2026-10-02 10:20'),
            ],
            [
                'id' => 2,
                'name' => 'Another Customer',
                'email' => 'another@example.com',
                'message' => 'My order has not arrived yet.',
                'submitted_at' => new \DateTimeImmutable('2026-10-03 08:05'),
            ],
        ];

        return $this->render('admin_message/index.html.twig', [
            'messages' => $messages,
        ]);
    }
}