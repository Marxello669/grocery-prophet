<?php

namespace App\Twig\Components;

use App\Enum\GroceryEnum;
use App\Repository\GroceryRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

#[AsLiveComponent]
final class GroceryTable
{
    use DefaultActionTrait;

    #[LiveProp]
    /**
     * @return GroceryEnum[]
     */
    public function getTypes(): array
    {
        return GroceryEnum::cases();
    }

    #[LiveProp(writable: true)]
    public int $page = 1;

    #[LiveProp(writable: true)]
    public int $quantity = 2;

    public function __construct(private GroceryRepository $groceryRepository)
    {
    }

    public function getEntries(): Paginator
    {
        $offset = ($this->page - 1) * $this->quantity;

        $query = $this->groceryRepository->createQueryBuilder('p')
            ->setFirstResult($offset)
            ->setMaxResults($this->quantity)
            ->orderBy('p.name', 'DESC')
            ->getQuery();

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
        // Add custom validation logic here
        $this->page = max(1, min($page, $this->getTotalPages()));
    }
}
