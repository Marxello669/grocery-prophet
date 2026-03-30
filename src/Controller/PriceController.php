<?php

namespace App\Controller;

use App\DTO\Price\NewDTO;
use App\Entity\Grocery;
use App\Entity\Price;
use App\Form\PriceEditType;
use App\Form\PriceType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/price')]
final class PriceController extends AbstractController
{
    #[Route('/', name: 'app_price_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('price/index.html.twig');
    }

    #[Route('/new/{grocery}', name: 'app_price_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ?Grocery $grocery = null): Response
    {
        $newDTO = new NewDTO($grocery);

        $form = $this->createForm(PriceType::class, $newDTO);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $price = $newDTO->toPrice();

            $entityManager->persist($price);
            $entityManager->flush();

            $this->addFlash('success', 'Price added successfully!');

            return $this->redirectToRoute('app_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('price/new.html.twig', [
            'dto' => $newDTO,
            'form' => $form->createView(),
            'grocery' => $grocery,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_price_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Price $price, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PriceEditType::class, $price);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Price updated successfully!');

            return $this->redirectToRoute('app_price_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('price/edit.html.twig', [
            'price' => $price,
            'form' => $form,
            'grocery' => $price->getGrocery(),
        ]);
    }
}
