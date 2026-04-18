<?php

namespace App\Repository;

use App\Entity\Grocery;
use App\Enum\GroceryEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Grocery>
 */
class GroceryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Grocery::class);
    }

    public function getPagination(
        string       $query,
        int          $offset,
        int          $quantity,
        ?GroceryEnum $type = null
    ): QueryBuilder
    {
        $qb = $this->createQueryBuilder('g')
            ->leftJoin('g.prices', 'p')
            ->where('g.name LIKE :query')
            ->setParameter('query', "%$query%")
            ->setFirstResult($offset)
            ->setMaxResults($quantity)
            ->orderBy('g.name', 'ASC');

        if (!is_null($type)) {
            $qb->andWhere('g.type = :type')
                ->setParameter('type', $type->value);
        }

        return $qb;
    }
}
