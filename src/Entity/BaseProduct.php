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
}
