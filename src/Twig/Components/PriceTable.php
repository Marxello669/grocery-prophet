<?php

namespace App\Twig\Components;

use App\Enum\ShopEnum;
use App\Repository\PriceRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

#[AsLiveComponent]
final class PriceTable
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public ?string $query = "";

    #[LiveProp(writable: true)]
    public int $page = 1;

    #[LiveProp(writable: true)]
    public int $quantity = 10;

    #[LiveProp(writable: true)]
    public ?ShopEnum $shop = null;

    #[LiveProp(writable: true)]
    public ?string $startDate = null;

    #[LiveProp(writable: true)]
    public ?string $endDate = null;

    public function __construct(
        private readonly PriceRepository $priceRepository
    ) {
    }

    /**
     * @return ShopEnum[]
     */
    public function getShops(): array
    {
        return ShopEnum::cases();
    }

    public function getEntries(): Paginator
    {
        $offset = ($this->page - 1) * $this->quantity;
        $this->query = $this->query ?? "";

        $query = $this->priceRepository->getPagination(
            $this->query,
            $offset,
            $this->quantity,
            $this->shop,
            $this->startDate,
            $this->endDate
        );

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
        $this->shop = null;
        $this->startDate = null;
        $this->endDate = null;
        $this->page = 1;
    }

    #[LiveAction]
    public function nextPage(): void
    {
        if ($this->hasNextPage()) {
            $this->page++;
        }
    }

    #[LiveAction]
    public function previousPage(): void
    {
        if ($this->hasPreviousPage()) {
            $this->page--;
        }
    }

    #[LiveAction]
    public function setPage(int $page): void
    {
        $this->page = max(1, min($page, $this->getTotalPages()));
    }
}
