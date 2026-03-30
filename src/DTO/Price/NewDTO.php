<?php

namespace App\DTO\Price;

use App\Entity\Grocery;
use App\Entity\Price;
use App\Enum\ShopEnum;
use App\Enum\UnitEnum;
use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;

class NewDTO
{
    #[Assert\Positive]
    public ?float $value = 0;

    #[Assert\NotBlank]
    public ?ShopEnum $shop = null;

    #[Assert\Positive]
    public ?float $quantity = 0;

    #[Assert\NotBlank]
    public ?UnitEnum $subUnit = null;

    #[Assert\NotBlank]
    public ?Grocery $grocery = null;

    public function __construct(?Grocery $grocery = null)
    {
        if ($grocery instanceof Grocery) {
            $this->grocery = $grocery;
        }
    }

    public function toPrice(): Price
    {
        $price = new Price();

        $calculatedValue = $this->convertUnit($this->value, $this->quantity, $this->grocery->getUnit(), $this->subUnit);

        $price->setValue($calculatedValue);

        $price->setCreatedAt(new DateTimeImmutable());
        $price->setShop($this->shop);
        $price->setGrocery($this->grocery);

        return $price;
    }

    private function convertUnit(float $value, float $quantity, UnitEnum $baseUnit, UnitEnum $subUnit): float
    {
        $quantityInBaseUnit = $quantity * $subUnit->getFactor();

        if ($quantityInBaseUnit === 0.0) {
            return 0.0;
        }

        return $value / $quantityInBaseUnit;
    }
}
