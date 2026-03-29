<?php

namespace App\Controller;

use App\Entity\Grocery;
use App\Entity\Price;
use App\Form\GroceryType;
use App\Repository\GroceryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

#[Route('/grocery')]
final class GroceryController extends AbstractController
{
    #[Route('/new', name: 'app_grocery_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $grocery = new Grocery();
        $form = $this->createForm(GroceryType::class, $grocery);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($grocery);
            $entityManager->flush();

            $this->addFlash('success', 'Grocery created successfully!');

            return $this->redirectToRoute('app_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('grocery/new.html.twig', [
            'grocery' => $grocery,
            'form' => $form,
        ]);
    }

    #[Route('/grocery/{id:grocery}', name: 'app_grocery_show')]
    public function show(Grocery $grocery): Response
    {
        // 1. Get current prices
        $currentPrices = [];
        $prices = $grocery->getPrices();

        // Group by shop and get the latest price for each
        $latestPricesByShop = [];
        foreach ($prices as $price) {
            $shopValue = $price->getShop()->value;
            if (!isset($latestPricesByShop[$shopValue]) || $price->getCreatedAt() > $latestPricesByShop[$shopValue]->getCreatedAt()) {
                $latestPricesByShop[$shopValue] = $price;
            }
        }

        $minPrice = null;
        $maxPrice = null;

        foreach ($latestPricesByShop as $price) {
            $val = (float)$price->getValue();
            if ($minPrice === null || $val < $minPrice) $minPrice = $val;
            if ($maxPrice === null || $val > $maxPrice) $maxPrice = $val;
        }

        foreach ($latestPricesByShop as $price) {
            $val = (float)$price->getValue();
            $currentPrices[] = [
                'price' => $val,
                'lowest' => $val === $minPrice,
                'highest' => $val === $maxPrice,
                'shop' => $price->getShop(),
            ];
        }

        return $this->render('grocery/show.html.twig', [
            'grocery' => $grocery,
            'currentPrices' => $currentPrices,
        ]);
    }

    #[Route('/{id:grocery}/edit', name: 'app_grocery_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Grocery $grocery, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(GroceryType::class, $grocery);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Grocery updated successfully!');

            return $this->redirectToRoute('app_grocery_show', ['id' => $grocery->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('grocery/edit.html.twig', [
            'grocery' => $grocery,
            'form' => $form,
        ]);
    }
}
