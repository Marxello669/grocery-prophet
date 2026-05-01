<?php

namespace App\Entity;

use App\Enum\UnitEnum;
use App\Repository\RecipeIngredientRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RecipeIngredientRepository::class)]
class RecipeIngredient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'ingredients')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Recipe $recipe = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?BaseProduct $baseProduct = null;

    #[ORM\Column]
    private ?float $quantity = null;

    #[ORM\Column(enumType: UnitEnum::class)]
    private ?UnitEnum $unit = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRecipe(): ?Recipe
    {
        return $this->recipe;
    }

    public function setRecipe(?Recipe $recipe): static
    {
        $this->recipe = $recipe;

        return $this;
    }

    public function getBaseProduct(): ?BaseProduct
    {
        return $this->baseProduct;
    }

    public function setBaseProduct(?BaseProduct $baseProduct): static
    {
        $this->baseProduct = $baseProduct;

        return $this;
    }

    public function getQuantity(): ?float
    {
        return $this->quantity;
    }

    public function setQuantity(float $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getUnit(): ?UnitEnum
    {
        return $this->unit;
    }

    public function setUnit(UnitEnum $unit): static
    {
        $this->unit = $unit;

        return $this;
    }

    /**
     * Get the lowest price for this ingredient across all groceries of the base product
     * Accounts for unit conversions between grocery unit and recipe ingredient unit
     */
    public function getLowestPrice(): float
    {
        if (!$this->baseProduct) {
            return 0;
        }

        $lowestCost = PHP_FLOAT_MAX;
        $hasPrice = false;

        foreach ($this->baseProduct->getGroceries() as $grocery) {
            $latestPrices = $this->getLatestPricesPerShop($grocery);
            if (empty($latestPrices)) {
                continue;
            }

            // Find the lowest price and convert units appropriately
            foreach ($latestPrices as $price) {
                $cost = $this->calculateCostForPrice($grocery, (float)$price->getValue());

                if ($cost < $lowestCost) {
                    $lowestCost = $cost;
                    $hasPrice = true;
                }
            }
        }

        return $hasPrice ? $lowestCost : 0;
    }

    /**
     * Get the shop with the lowest price for this ingredient
     * Accounts for unit conversions between grocery unit and recipe ingredient unit
     */
    public function getLowestPriceShop(): ?string
    {
        if (!$this->baseProduct) {
            return null;
        }

        $lowestCost = PHP_FLOAT_MAX;
        $lowestShop = null;

        foreach ($this->baseProduct->getGroceries() as $grocery) {
            $latestPrices = $this->getLatestPricesPerShop($grocery);
            if (empty($latestPrices)) {
                continue;
            }

            // Find the lowest price and shop
            foreach ($latestPrices as $price) {
                $cost = $this->calculateCostForPrice($grocery, (float)$price->getValue());

                if ($cost < $lowestCost) {
                    $lowestCost = $cost;
                    $lowestShop = $price->getShop()->label();
                }
            }
        }

        return $lowestShop;
    }

    /**
     * Get the price per unit of the grocery with the lowest total cost
     * Returns the actual grocery's unit price (e.g., €5 per kg), not the total cost
     */
    public function getLowestPricePerUnit(): float
    {
        if (!$this->baseProduct) {
            return 0;
        }

        $lowestCost = PHP_FLOAT_MAX;
        $lowestUnitPrice = 0;

        foreach ($this->baseProduct->getGroceries() as $grocery) {
            $latestPrices = $this->getLatestPricesPerShop($grocery);
            if (empty($latestPrices)) {
                continue;
            }

            // Find the lowest price and track its unit price
            foreach ($latestPrices as $price) {
                $priceValue = (float)$price->getValue();
                $cost = $this->calculateCostForPrice($grocery, $priceValue);

                if ($cost < $lowestCost) {
                    $lowestCost = $cost;
                    $lowestUnitPrice = $priceValue;
                }
            }
        }

        return $lowestUnitPrice;
    }

    /**
     * Get the latest price for each shop from a grocery's prices
     *
     * @return array<int, Price>
     */
    private function getLatestPricesPerShop(Grocery $grocery): array
    {
        $prices = $grocery->getPrices();
        if ($prices->isEmpty()) {
            return [];
        }

        $latestPrices = [];
        foreach ($prices as $price) {
            $shopValue = $price->getShop()->value;
            if (!isset($latestPrices[$shopValue]) || $price->getCreatedAt() > $latestPrices[$shopValue]->getCreatedAt()) {
                $latestPrices[$shopValue] = $price;
            }
        }

        return $latestPrices;
    }

    /**
     * Calculate cost for this ingredient given a grocery and its unit price
     * Handles unit conversion from recipe unit to grocery unit
     */
    private function calculateCostForPrice(Grocery $grocery, float $priceValue): float
    {
        $groceryUnit = $grocery->getUnit();
        $convertedQuantity = $this->quantity * $this->unit->getFactor() / $groceryUnit->getFactor();
        return $priceValue * $convertedQuantity;
    }
}
