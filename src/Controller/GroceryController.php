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

            return $this->redirectToRoute('app_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('grocery/new.html.twig', [
            'grocery' => $grocery,
            'form' => $form,
        ]);
    }

    #[Route('/grocery/{id:grocery}', name: 'app_grocery_show')]
    public function show(Grocery $grocery, ChartBuilderInterface $chartBuilder): Response
    {
        // 1. Mock/Fetch your data
        $labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'];
        $historyData = [
            ['store' => 'Continente', 'color' => '#1D9E75', 'data' => [0.95, 0.92, 0.89, 0.91, 0.88, 0.89, 0.89]],
            ['store' => 'Pingo Doce', 'color' => '#378ADD', 'data' => [1.05, 1.02, 0.99, 1.05, 1.02, 0.99, 0.99]],
            ['store' => 'Lidl',       'color' => '#D85A30', 'data' => [1.09, 1.15, 1.19, 1.10, 1.15, 1.13, 1.13]],
        ];

        // 2. Build the Chart
        $chart = $chartBuilder->createChart(Chart::TYPE_LINE);

        $datasets = [];
        foreach ($historyData as $ds) {
            $datasets[] = [
                'label' => $ds['store'],
                'data' => $ds['data'],
                'borderColor' => $ds['color'],
                'backgroundColor' => 'transparent',
                'pointBackgroundColor' => $ds['color'],
                'tension' => 0.35,
                'borderWidth' => 2,
                'pointRadius' => 3,
            ];
        }

        $chart->setData([
            'labels' => $labels,
            'datasets' => $datasets,
        ]);

        // 3. Set Options (Mirroring your JS config)
        $chart->setOptions([
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => ['display' => false], // Using your custom DaisyUI legend instead
            ],
            'scales' => [
                'y' => [
                    'min' => 0.70,
                    'max' => 1.40,
                    'ticks' => ['callback' => 'function(value) { return "€" + value.toFixed(2); }']
                ],
            ],
        ]);

        /*
             {# Derived stats #}
            {% if currentPrices is not defined %}
        {% set currentPrices = [
            { store: 'Continente', price: '0.89', lowest: true,  highest: false },
            { store: 'Pingo Doce', price: '0.99', lowest: false, highest: false },
            { store: 'Lidl',       price: '1.13', lowest: false, highest: true  }
        ] %}
    {% endif %}
        
    {% set lowestPrice  = currentPrices|filter(p => p.lowest)|first %}
    {% set highestPrice = currentPrices|filter(p => p.highest)|first %}
    {% set avgPrice = (currentPrices|reduce((carry, p) => carry + p.price|number_format(2), 0) / currentPrices|length)|number_format(2) %}
    {% set spread = (highestPrice.price - lowestPrice.price)|number_format(2) %}

         */

        return $this->render('grocery/show.html.twig', [
            'grocery' => $grocery,
            'chart' => $chart,
            'historyData' => $historyData,
        ]);
    }
}
