<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Entity\RecipeIngredient;
use App\Form\RecipeIngredientType;
use App\Form\RecipeType;
use App\Repository\RecipeIngredientRepository;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/recipe')]
final class RecipeController extends AbstractController
{
    #[Route('', name: 'app_recipe_index', methods: ['GET'])]
    public function index(RecipeRepository $repository): Response
    {
        $recipes = $repository->findAllOrdered();

        return $this->render('recipe/index.html.twig', [
            'recipes' => $recipes,
        ]);
    }

    #[Route('/new', name: 'app_recipe_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($recipe);
            $entityManager->flush();

            $this->addFlash('success', 'Recipe created successfully!');

            return $this->redirectToRoute('app_recipe_show', ['id' => $recipe->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('recipe/new.html.twig', [
            'recipe' => $recipe,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_recipe_show')]
    public function show(Recipe $recipe): Response
    {
        $ingredient = new RecipeIngredient();
        $ingredient->setRecipe($recipe);
        $ingredientForm = $this->createForm(RecipeIngredientType::class, $ingredient);
        
        $editForm = $this->createForm(RecipeType::class, $recipe);

        // Create edit forms for each ingredient
        $ingredientEditForms = [];
        foreach ($recipe->getIngredients() as $recipeIngredient) {
            $form = $this->createForm(RecipeIngredientType::class, $recipeIngredient);
            $ingredientEditForms[$recipeIngredient->getId()] = $form->createView();
        }

        return $this->render('recipe/show.html.twig', [
            'recipe' => $recipe,
            'ingredient_form' => $ingredientForm,
            'edit_form' => $editForm,
            'ingredient_edit_forms' => $ingredientEditForms,
        ]);
    }

    #[Route('/{id}/scale', name: 'app_recipe_scale', methods: ['GET'])]
    public function scale(Recipe $recipe, Request $request): Response
    {
        $factor = (float)$request->query->get('factor', 1.0);
        
        // Prevent invalid scaling factors
        if ($factor <= 0) {
            $factor = 1.0;
        }
        if ($factor > 10) {
            $factor = 10;
        }

        $scaledRecipe = clone $recipe;
        $scaledIngredients = [];

        // Scale all ingredients
        foreach ($recipe->getIngredients() as $ingredient) {
            $lowestPrice = $ingredient->getLowestPrice();
            $scaled = [
                'original' => $ingredient,
                'scaled_quantity' => round($ingredient->getQuantity() * $factor, 2),
                'factor' => $factor,
                'lowest_price' => $lowestPrice,
                'scaled_price' => round($lowestPrice * $factor, 2),
            ];
            $scaledIngredients[] = $scaled;
        }

        // Calculate scaled recipe metrics
        $scaledServings = $recipe->getServings() ? (int)($recipe->getServings() * $factor) : null;
        $scaledCost = $recipe->getTotalCost() * $factor;

        return $this->render('recipe/scale.html.twig', [
            'recipe' => $recipe,
            'scaled_recipe' => $scaledRecipe,
            'scaled_ingredients' => $scaledIngredients,
            'factor' => $factor,
            'scaled_servings' => $scaledServings,
            'scaled_cost' => $scaledCost,
            'original_cost' => $recipe->getTotalCost(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_recipe_edit', methods: ['POST'])]
    public function edit(Request $request, Recipe $recipe, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Recipe updated successfully!');
        }

        return $this->redirectToRoute('app_recipe_show', ['id' => $recipe->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/delete', name: 'app_recipe_delete', methods: ['POST'])]
    public function delete(Request $request, Recipe $recipe, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $recipe->getId(), $request->request->get('_token'))) {
            $entityManager->remove($recipe);
            $entityManager->flush();

            $this->addFlash('success', 'Recipe deleted successfully!');
        }

        return $this->redirectToRoute('app_recipe_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/ingredient/add', name: 'app_recipe_ingredient_add', methods: ['GET', 'POST'])]
    public function addIngredient(Request $request, Recipe $recipe, EntityManagerInterface $entityManager): Response
    {
        $ingredient = new RecipeIngredient();
        $ingredient->setRecipe($recipe);
        $form = $this->createForm(RecipeIngredientType::class, $ingredient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($ingredient);
            $entityManager->flush();

            $this->addFlash('success', 'Ingredient added successfully!');

            return $this->redirectToRoute('app_recipe_show', ['id' => $recipe->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('recipe/ingredient_form.html.twig', [
            'recipe' => $recipe,
            'ingredient' => $ingredient,
            'form' => $form,
            'action' => 'add',
        ]);
    }

    #[Route('/ingredient/{id}/edit', name: 'app_recipe_ingredient_edit', methods: ['GET', 'POST'])]
    public function editIngredient(Request $request, RecipeIngredient $ingredient, EntityManagerInterface $entityManager): Response
    {
        $recipe = $ingredient->getRecipe();
        $form = $this->createForm(RecipeIngredientType::class, $ingredient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Ingredient updated successfully!');

            return $this->redirectToRoute('app_recipe_show', ['id' => $recipe->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('recipe/ingredient_form.html.twig', [
            'recipe' => $recipe,
            'ingredient' => $ingredient,
            'form' => $form,
            'action' => 'edit',
        ]);
    }

    #[Route('/ingredient/{id}/delete', name: 'app_recipe_ingredient_delete', methods: ['POST'])]
    public function deleteIngredient(Request $request, RecipeIngredient $ingredient, EntityManagerInterface $entityManager): Response
    {
        $recipeId = $ingredient->getRecipe()->getId();

        if ($this->isCsrfTokenValid('delete' . $ingredient->getId(), $request->request->get('_token'))) {
            $entityManager->remove($ingredient);
            $entityManager->flush();

            $this->addFlash('success', 'Ingredient deleted successfully!');
        }

        return $this->redirectToRoute('app_recipe_show', ['id' => $recipeId], Response::HTTP_SEE_OTHER);
    }
}
