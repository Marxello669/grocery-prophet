<?php

namespace App\Service;

use App\Entity\BaseProduct;
use App\Entity\Grocery;
use App\Entity\Recipe;
use App\Entity\RecipeIngredient;
use App\Enum\UnitEnum;

/**
 * Service for calculating prices based on quantities and unit conversions
 */
class PriceCalculationService
{
    public function __construct(
        private PriceFilteringService $priceFilteringService,
    ) {
    }

    /**
     * Get the lowest price for a recipe ingredient across all grocery varieties
     * Accounts for unit conversions between grocery unit and recipe ingredient unit
     */
    public function getLowestPriceForIngredient(RecipeIngredient $ingredient): float
    {
        $baseProduct = $ingredient->getBaseProduct();
        if (!$baseProduct) {
            return 0;
        }

        $lowestCost = PHP_FLOAT_MAX;
        $hasPrice = false;

        foreach ($baseProduct->getGroceries() as $grocery) {
            $prices = $grocery->getPrices();
            if ($prices->isEmpty()) {
                continue;
            }

            // Get the latest price for each shop
            $latestPrices = $this->priceFilteringService->getLatestPricePerShop($prices);

            // Find the lowest price and convert units appropriately
            foreach ($latestPrices as $price) {
                $cost = $this->calculateIngredientCost($ingredient, $grocery, (float)$price->getValue());
                if ($cost < $lowestCost) {
                    $lowestCost = $cost;
                    $hasPrice = true;
                }
            }
        }

        return $hasPrice ? $lowestCost : 0;
    }

    /**
     * Get the shop with the lowest price for a recipe ingredient
     * Accounts for unit conversions between grocery unit and recipe ingredient unit
     */
    public function getLowestPriceShopForIngredient(RecipeIngredient $ingredient): ?string
    {
        $baseProduct = $ingredient->getBaseProduct();
        if (!$baseProduct) {
            return null;
        }

        $lowestCost = PHP_FLOAT_MAX;
        $lowestShop = null;

        foreach ($baseProduct->getGroceries() as $grocery) {
            $prices = $grocery->getPrices();
            if ($prices->isEmpty()) {
                continue;
            }

            // Get the latest price for each shop
            $latestPrices = $this->priceFilteringService->getLatestPricePerShop($prices);

            // Find the lowest price and shop
            foreach ($latestPrices as $price) {
                $cost = $this->calculateIngredientCost($ingredient, $grocery, (float)$price->getValue());
                if ($cost < $lowestCost) {
                    $lowestCost = $cost;
                    $lowestShop = $price->getShop()->label();
                }
            }
        }

        return $lowestShop;
    }

    /**
     * Get the lowest price for a base product (any grocery variety)
     */
    public function getLowestPriceForBaseProduct(BaseProduct $baseProduct): float
    {
        $lowestPrice = PHP_FLOAT_MAX;
        $hasPrice = false;

        foreach ($baseProduct->getGroceries() as $grocery) {
            $prices = $grocery->getPrices();
            if ($prices->isEmpty()) {
                continue;
            }

            // Get the latest price for each shop
            $latestPrices = $this->priceFilteringService->getLatestPricePerShop($prices);

            // Find the lowest price
            foreach ($latestPrices as $price) {
                $priceValue = (float)$price->getValue();
                if ($priceValue < $lowestPrice) {
                    $lowestPrice = $priceValue;
                    $hasPrice = true;
                }
            }
        }

        return $hasPrice ? $lowestPrice : 0;
    }

    /**
     * Get the lowest price for a specific grocery across all shops
     */
    public function getLowestPriceForGrocery(Grocery $grocery): float
    {
        $prices = $grocery->getPrices();
        if ($prices->isEmpty()) {
            return 0;
        }

        // Get the latest price for each shop
        $latestPrices = $this->priceFilteringService->getLatestPricePerShop($prices);

        // Find the lowest price
        $lowestPrice = PHP_FLOAT_MAX;
        foreach ($latestPrices as $price) {
            $priceValue = (float)$price->getValue();
            if ($priceValue < $lowestPrice) {
                $lowestPrice = $priceValue;
            }
        }

        return $lowestPrice < PHP_FLOAT_MAX ? $lowestPrice : 0;
    }

    /**
     * Get the shop with the lowest price for a specific grocery
     */
    public function getLowestShopForGrocery(Grocery $grocery): ?string
    {
        $prices = $grocery->getPrices();
        if ($prices->isEmpty()) {
            return null;
        }

        // Get the latest price for each shop
        $latestPrices = $this->priceFilteringService->getLatestPricePerShop($prices);

        // Find the lowest price
        $lowestPrice = PHP_FLOAT_MAX;
        $lowestShop = null;

        foreach ($latestPrices as $price) {
            $priceValue = (float)$price->getValue();
            if ($priceValue < $lowestPrice) {
                $lowestPrice = $priceValue;
                $lowestShop = $price->getShop()->label();
            }
        }

        return $lowestShop;
    }

    /**
     * Get total cost for an entire recipe
     */
    public function getTotalCostForRecipe(Recipe $recipe): float
    {
        $totalCost = 0;

        foreach ($recipe->getIngredients() as $ingredient) {
            $totalCost += $this->getLowestPriceForIngredient($ingredient);
        }

        return $totalCost;
    }

    /**
     * Calculate the cost for an ingredient given a price per unit
     * Handles unit conversion from recipe unit to grocery unit
     *
     * @param RecipeIngredient $ingredient The recipe ingredient
     * @param Grocery $grocery The grocery (provides the unit)
     * @param float $pricePerUnit Price per unit of the grocery
     */
    private function calculateIngredientCost(RecipeIngredient $ingredient, Grocery $grocery, float $pricePerUnit): float
    {
        // Convert recipe quantity from its unit to the grocery's unit
        // Formula: recipeQuantity * recipeUnit.getFactor() / groceryUnit.getFactor()
        $convertedQuantity = $ingredient->getQuantity() * $ingredient->getUnit()->getFactor() / $grocery->getUnit()->getFactor();

        // Calculate the actual cost for this quantity
        return $pricePerUnit * $convertedQuantity;
    }
}
