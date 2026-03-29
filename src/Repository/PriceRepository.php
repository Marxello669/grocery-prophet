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
}
