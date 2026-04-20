<?php

namespace App\Repository;

use App\Entity\ShoppingListItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ShoppingListItem>
 */
class ShoppingListItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShoppingListItem::class);
    }

    /**
     * Find all shopping list items with their groceries and latest prices eagerly loaded
     * This prevents N+1 queries by using a single DQL query with LEFT JOIN
     *
     * @return ShoppingListItem[]
     */
    public function findAllWithPrices(): array
    {
        return $this->createQueryBuilder('item')
            ->leftJoin('item.grocery', 'grocery')
            ->leftJoin('grocery.prices', 'prices')
            ->addSelect('grocery', 'prices')
            ->orderBy('item.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
