<?php

namespace App\Entity;

use App\Repository\ShoppingListItemRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ShoppingListItemRepository::class)]
class ShoppingListItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Grocery $grocery = null;

    #[ORM\Column]
    private ?int $quantity = 1;

    #[ORM\Column]
    private ?bool $checked = false;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function isChecked(): ?bool
    {
        return $this->checked;
    }

    public function setChecked(bool $checked): static
    {
        $this->checked = $checked;

        return $this;
    }
}
