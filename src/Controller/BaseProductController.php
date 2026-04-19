<?php

namespace App\Controller;

use App\Entity\BaseProduct;
use App\Form\BaseProductType;
use App\Repository\BaseProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/base-product')]
final class BaseProductController extends AbstractController
{
    #[Route('', name: 'app_base_product_index', methods: ['GET'])]
    public function index(BaseProductRepository $repository): Response
    {
        $baseProducts = $repository->findAll();

        return $this->render('base_product/index.html.twig', [
            'base_products' => $baseProducts,
        ]);
    }

    #[Route('/new', name: 'app_base_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $baseProduct = new BaseProduct();
        $form = $this->createForm(BaseProductType::class, $baseProduct);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($baseProduct);
            $entityManager->flush();

            $this->addFlash('success', 'Base product created successfully!');

            return $this->redirectToRoute('app_base_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('base_product/new.html.twig', [
            'base_product' => $baseProduct,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_base_product_show')]
    public function show(BaseProduct $baseProduct): Response
    {
        return $this->render('base_product/show.html.twig', [
            'base_product' => $baseProduct,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_base_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, BaseProduct $baseProduct, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BaseProductType::class, $baseProduct);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Base product updated successfully!');

            return $this->redirectToRoute('app_base_product_show', ['id' => $baseProduct->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('base_product/edit.html.twig', [
            'base_product' => $baseProduct,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_base_product_delete', methods: ['POST'])]
    public function delete(Request $request, BaseProduct $baseProduct, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $baseProduct->getId(), $request->request->get('_token'))) {
            $entityManager->remove($baseProduct);
            $entityManager->flush();

            $this->addFlash('success', 'Base product deleted successfully!');
        }

        return $this->redirectToRoute('app_base_product_index', [], Response::HTTP_SEE_OTHER);
    }
}
