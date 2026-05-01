<?php

namespace App\Repository;

use App\Entity\Recipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Recipe>
 */
class RecipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recipe::class);
    }

    /**
     * Find all recipes ordered by name with eager loaded ingredients and prices
     * This prevents N+1 queries when accessing ingredients and their prices
     */
    public function findAllOrdered()
    {
        return $this->createQueryBuilder('r')
            ->leftJoin('r.ingredients', 'i')
            ->addSelect('i')
            ->leftJoin('i.baseProduct', 'bp')
            ->addSelect('bp')
            ->leftJoin('bp.groceries', 'g')
            ->addSelect('g')
            ->leftJoin('g.prices', 'p')
            ->addSelect('p')
            ->orderBy('r.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
