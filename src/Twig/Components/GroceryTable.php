<?php

namespace App\Twig\Components;

use App\Entity\Grocery;
use App\Entity\Price;
use App\Enum\GroceryEnum;
use App\Repository\GroceryRepository;
use App\Repository\PriceRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

#[AsLiveComponent]
final class GroceryTable
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public ?string $query = "";

    #[LiveProp(writable: true)]
    public int $page = 1;

    #[LiveProp(writable: true)]
    public int $quantity = 5;

    #[LiveProp(writable: true)]
    public ?GroceryEnum $type = null;

    public function __construct(
        private readonly GroceryRepository $groceryRepository,
        private readonly PriceRepository   $priceRepository
    )
    {
    }

    public function getLowestPrice(Grocery $grocery): ?Price
    {
        return $this->priceRepository->findLowestCurrentPriceForGrocery($grocery);
    }

    /**
     * @return GroceryEnum[]
     */
    public function getTypes(): array
    {
        return GroceryEnum::cases();
    }


    public function getEntries(): Paginator
    {
        $offset = ($this->page - 1) * $this->quantity;

        $this->query = $this->query ?? "";

        $query = $this->groceryRepository->getPagination($this->query, $offset, $this->quantity, $this->type);

        return new Paginator($query);
    }

    public function getTotalPages(): int
    {
        $totalItems = count($this->getEntries());

        return (int)ceil($totalItems / $this->quantity);
    }

    public function hasNextPage(): bool
    {
        return $this->page < $this->getTotalPages();
    }

    public function hasPreviousPage(): bool
    {
        return $this->page > 1;
    }

    #[LiveAction]
    public function clearFilters(): void
    {
        $this->query = "";
        $this->type = null;
        $this->page = 1;
    }

    #[LiveAction]
    public function nextPage(): void
    {
        if ($this->page < $this->getTotalPages()) {
            $this->page++;
        }
    }

    #[LiveAction]
    public function previousPage(): void
    {
        if ($this->page > 1) {
            $this->page--;
        }
    }

    #[LiveAction]
    public function setPage(int $page): void
    {
        $this->page = max(1, min($page, $this->getTotalPages()));
    }
}
