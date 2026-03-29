<?php

namespace App\Twig\Components;

use App\Enum\ShopEnum;
use App\Entity\Grocery;
use App\Repository\PriceRepository;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
class PriceChart
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public string $timeframe = '1m'; // Default to 1 month

    #[LiveAction]
    public function setTimeframe(#[LiveArg] string $timeframe): void
    {
        $this->timeframe = $timeframe;
    }

    #[LiveProp]
    public Grocery $grocery;

    public function __construct(
        private readonly PriceRepository $repository,
        private readonly ChartBuilderInterface $chartBuilder
    )
    {
    }

    public function getChart(): Chart
    {
        $startDate = match ($this->timeframe) {
            '1w' => new \DateTimeImmutable('-7 days 00:00:00'),
            '1m' => new \DateTimeImmutable('-30 days 00:00:00'),
            default => null, // All time
        };

        $prices = $this->repository->findPriceHistory($this->grocery, $startDate);

        $labels = [];
        $datasetsByShop = [];

        // Colors for different shops
        $colors = ShopEnum::colors();

        // Group prices by timestamp and shop
        $groupedPrices = [];
        foreach ($prices as $price) {
            $date = $price->getCreatedAt()->format('M d, Y');
            $shopKey = $price->getShop()->value;
            $groupedPrices[$date][$shopKey] = (float)$price->getValue();

            if (!in_array($date, $labels)) {
                $labels[] = $date;
            }
        }

        foreach ($labels as $date) {
            foreach ($datasetsByShop as $shopKey => &$dataset) {
                if (isset($groupedPrices[$date][$shopKey])) {
                    $dataset['data'][] = $groupedPrices[$date][$shopKey];
                } else {
                    $dataset['data'][] = null;
                }
            }

            // Also check for any new shops on this date
            foreach ($groupedPrices[$date] as $shopKey => $value) {
                if (!isset($datasetsByShop[$shopKey])) {
                    $shop = ShopEnum::from($shopKey);
                    $datasetsByShop[$shopKey] = [
                        'label' => $shop->label(),
                        'borderColor' => $colors[$shopKey],
                        'backgroundColor' => ($colors[$shopKey]) . '33',
                        'data' => array_merge(array_fill(0, array_search($date, $labels), null), [$value]),
                        'tension' => 0.3,
                        'fill' => false,
                    ];
                }
            }
        }

        $chart = $this->chartBuilder->createChart(Chart::TYPE_LINE);
        $chart->setData([
            'labels' => $labels,
            'datasets' => array_values($datasetsByShop),
        ]);

        $chart->setOptions([
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => ['display' => false],
                'tooltip' => [
                    'mode' => 'index',
                    'intersect' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => false,
                    'ticks' => ['callback' => "function(v){return '€'+v.toFixed(2)}"]
                ],
                'x' => [
                    'grid' => ['display' => false]
                ]
            ]
        ]);

        return $chart;
    }
}
