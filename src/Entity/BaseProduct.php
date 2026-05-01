<?php

namespace App\Entity;

use App\Repository\BaseProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BaseProductRepository::class)]
class BaseProduct
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, Grocery>
     */
    #[ORM\OneToMany(targetEntity: Grocery::class, mappedBy: 'baseProduct')]
    private Collection $groceries;

    public function __construct()
    {
        $this->groceries = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Grocery>
     */
    public function getGroceries(): Collection
    {
        return $this->groceries;
    }

    public function addGrocery(Grocery $grocery): static
    {
        if (!$this->groceries->contains($grocery)) {
            $this->groceries->add($grocery);
            $grocery->setBaseProduct($this);
        }

        return $this;
    }

    public function removeGrocery(Grocery $grocery): static
    {
        if ($this->groceries->removeElement($grocery)) {
            // set the owning side to null (unless already changed)
            if ($grocery->getBaseProduct() === $this) {
                $grocery->setBaseProduct(null);
            }
        }

        return $this;
    }

    /**
     * Get the lowest price across all groceries and their latest prices
     */
    public function getLowestPrice(): float
    {
        $lowestPrice = PHP_FLOAT_MAX;
        $hasPrice = false;

        foreach ($this->groceries as $grocery) {
            $latestPrices = $this->getLatestPricesPerShop($grocery);
            if (empty($latestPrices)) {
                continue;
            }

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
     * Get the lowest price and corresponding shop for a specific grocery
     */
    public function getGroceryLowestPrice(Grocery $grocery): float
    {
        $latestPrices = $this->getLatestPricesPerShop($grocery);
        if (empty($latestPrices)) {
            return 0;
        }

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
    public function getGroceryLowestShop(Grocery $grocery): ?string
    {
        $latestPrices = $this->getLatestPricesPerShop($grocery);
        if (empty($latestPrices)) {
            return null;
        }

        // Find the lowest price and shop
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
}
