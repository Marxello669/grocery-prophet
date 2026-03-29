<?php

namespace App\Repository;

use App\Entity\Grocery;
use App\Entity\Price;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Price>
 */
class PriceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Price::class);
    }

    public function findPriceHistory(Grocery $grocery, ?\DateTime $date)
    {
        $qb = $this->createQueryBuilder('p')
            ->innerJoin('p.grocery', 'grocery')
            ->andWhere('grocery = :grocery')
            ->setParameter('grocery', $grocery);

        if ($date !== null) {
            $qb->andWhere('p.created_at >= :date')
                ->setParameter('date', $date);
        }

        return $qb->orderBy('p.created_at', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Finds the current lowest price for a grocery item.
     * Logic:
     * 1. Get the latest price for each shop.
     * 2. Find the minimum value among those.
     */
    public function findLowestCurrentPriceForGrocery(Grocery $grocery): ?Price
    {
        $subQuery = $this->getEntityManager()->createQueryBuilder()
            ->select('MAX(p2.created_at)')
            ->from(Price::class, 'p2')
            ->where('p2.grocery = :grocery')
            ->andWhere('p2.shop = p.shop');

        $latestPrices = $this->createQueryBuilder('p')
            ->where('p.grocery = :grocery')
            ->andWhere('p.created_at = (' . $subQuery->getDQL() . ')')
            ->setParameter('grocery', $grocery)
            ->getQuery()
            ->getResult();

        if (empty($latestPrices)) {
            return null;
        }

        $lowestPrice = null;
        foreach ($latestPrices as $price) {
            if ($lowestPrice === null || (float)$price->getValue() < (float)$lowestPrice->getValue()) {
                $lowestPrice = $price;
            }
        }

        return $lowestPrice;
    }
}
