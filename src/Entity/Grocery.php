<?php

namespace App\Entity;

use App\Enum\GroceryEnum;
use App\Enum\UnitEnum;
use App\Repository\GroceryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GroceryRepository::class)]
class Grocery
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: 'string', enumType: GroceryEnum::class)]
    private ?GroceryEnum $type = null;

    #[ORM\Column(enumType: UnitEnum::class)]
    private ?UnitEnum $unit = null;

    /**
     * @var Collection<int, Price>
     */
    #[ORM\OneToMany(targetEntity: Price::class, mappedBy: 'grocery')]
    private Collection $prices;

    #[ORM\ManyToOne(inversedBy: 'groceries')]
    private ?BaseProduct $baseProduct = null;

    public function __construct()
    {
        $this->prices = new ArrayCollection();
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

    public function getType(): ?GroceryEnum
    {
        return $this->type;
    }

    public function setType(GroceryEnum $type): static
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection<int, Price>
     */
    public function getPrices(): Collection
    {
        return $this->prices;
    }

    public function addPrice(Price $price): static
    {
        if (!$this->prices->contains($price)) {
            $this->prices->add($price);
            $price->setGrocery($this);
        }

        return $this;
    }

    public function removePrice(Price $price): static
    {
        if ($this->prices->removeElement($price)) {
            // set the owning side to null (unless already changed)
            if ($price->getGrocery() === $this) {
                $price->setGrocery(null);
            }
        }

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

    public function getBaseProduct(): ?BaseProduct
    {
        return $this->baseProduct;
    }

    public function setBaseProduct(?BaseProduct $baseProduct): static
    {
        $this->baseProduct = $baseProduct;

        return $this;
    }
}
