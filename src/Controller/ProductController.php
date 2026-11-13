<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/product')]
final class ProductController extends AbstractController
{
    #[Route('', name: 'app_product_index', methods: ['GET'])]
    public function index(Request $request, ProductRepository $productRepository, CategoryRepository $categoryRepository): Response
    {
        $q = trim((string) $request->query->get('q', ''));
        $category = $request->query->get('category');
        $status = (string) $request->query->get('status', '');

        return $this->render('product/index.html.twig', [
            'products' => $productRepository->search($q, $category ? (int) $category : null, $status),
            'categories' => $categoryRepository->findAll(),
            'q' => $q,
            'selectedCategory' => $category ? (int) $category : null,
            'status' => $status,
        ]);
    }

    #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->handleImageUpload($form->get('imageFile')->getData(), $product, $slugger);
            $product->setCreatedBy($this->getUser());
            $entityManager->persist($product);
            $entityManager->flush();

            $this->addFlash('success', 'Product created.');

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $this->denyUnlessOwnerOrAdmin($product);

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->handleImageUpload($form->get('imageFile')->getData(), $product, $slugger);
            $entityManager->flush();

            $this->addFlash('success', 'Product updated.');

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        $this->denyUnlessOwnerOrAdmin($product);

        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();

            $this->addFlash('success', 'Product deleted.');
        }

        return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
    }

    private function denyUnlessOwnerOrAdmin(Product $product): void
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            return;
        }

        if ($product->getCreatedBy() !== $this->getUser()) {
            throw $this->createAccessDeniedException('You can only modify your own products.');
        }
    }

    private function handleImageUpload(?UploadedFile $file, Product $product, SluggerInterface $slugger): void
    {
        if (!$file) {
            return;
        }

        $safeName = $slugger->slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $newName = $safeName . '-' . uniqid() . '.' . $file->guessExtension();

        try {
            $file->move($this->getParameter('kernel.project_dir') . '/public/uploads/products', $newName);
            $product->setImage($newName);
        } catch (FileException $e) {
            $this->addFlash('error', 'The image could not be uploaded.');
        }
    }
}