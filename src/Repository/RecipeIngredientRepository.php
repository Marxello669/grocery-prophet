<?php

namespace App\Repository;

use App\Entity\Recipe;
use App\Entity\RecipeIngredient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RecipeIngredient>
 */
class RecipeIngredientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RecipeIngredient::class);
    }

    /**
     * Find all ingredients for a recipe
     */
    public function findByRecipe(Recipe $recipe)
    {
        return $this->createQueryBuilder('ri')
            ->where('ri.recipe = :recipe')
            ->setParameter('recipe', $recipe)
            ->orderBy('ri.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
