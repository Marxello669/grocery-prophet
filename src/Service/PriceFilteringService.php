<?php

namespace App\Service;

use App\Entity\Price;
use Doctrine\Common\Collections\Collection;

/**
 * Service for filtering and organizing prices
 */
class PriceFilteringService
{
    /**
     * Get the latest price for each shop from a collection of prices
     * Returns array where keys are shop values and values are latest Price objects for that shop
     *
     * @param Collection<int, Price> $prices
     * @return array<string, Price>
     */
    public function getLatestPricePerShop(Collection $prices): array
    {
        $latestPrices = [];

        foreach ($prices as $price) {
            $shopValue = $price->getShop()->value;
            if (!isset($latestPrices[$shopValue]) || $price->getCreatedAt() > $latestPrices[$shopValue]->getCreatedAt()) {
                $latestPrices[$shopValue] = $price;
            }
        }

        return $latestPrices;
    }
}
