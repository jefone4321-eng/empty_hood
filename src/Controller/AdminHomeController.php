<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminHomeController extends AbstractController
{
    #[Route('/admin', name: 'app_admin_home')]
    public function index(): Response
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirect('/admin/dashboard');
        }

        return $this->redirectToRoute('app_product_index');
        return $this->redirectToRoute('app_admin_home');
    }
}