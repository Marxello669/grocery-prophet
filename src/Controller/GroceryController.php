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

            return $this->redirectToRoute('app_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('grocery/new.html.twig', [
            'grocery' => $grocery,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_grocery_show', methods: ['GET'])]
    public function show(Grocery $grocery, EntityManagerInterface $entityManager): Response
    {

        // In your controller, serialize PriceHistory grouped by store:
        $history = $entityManager->getRepository(Price::class)->findBy(['grocery' => $grocery]);

        // Shape it for Twig like this:
        $chartDatasets = [];
        $colors = ['#1D9E75', '#378ADD', '#D85A30', '#7F77DD'];
        $i = 0;
        foreach ($history as $store => $entries) {
            $chartDatasets[] = [
                'store' => $store,
                'color' => $colors[$i++ % count($colors)],
                'data' => array_column($entries, 'price'),
            ];
        }

        return $this->render('grocery/show.html.twig', [
            'grocery' => $grocery,
            'chartDatasets' => $chartDatasets,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_grocery_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Grocery $grocery, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(GroceryType::class, $grocery);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('grocery/edit.html.twig', [
            'grocery' => $grocery,
            'form' => $form,
        ]);
    }
}
