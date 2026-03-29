<?php

namespace App\Entity;

use App\Enum\GroceryEnum;
use App\Enum\ShopEnum;
use App\Repository\PriceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PriceRepository::class)]
class Price
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $value = null;

    #[ORM\Column(type: 'string', enumType: ShopEnum::class)]
    private ?ShopEnum $shop = null;

    #[ORM\Column(type: 'date_immutable')]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\ManyToOne(inversedBy: 'prices')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Grocery $grocery = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getShop(): ?ShopEnum
    {
        return $this->shop;
    }

    public function setShop(ShopEnum $shop): static
    {
        $this->shop = $shop;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getGrocery(): ?Grocery
    {
        return $this->grocery;
    }

    public function setGrocery(?Grocery $grocery): static
    {
        $this->grocery = $grocery;

        return $this;
    }
}
