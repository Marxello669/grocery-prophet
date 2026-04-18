<?php

namespace App\Controller;

use App\Entity\Grocery;
use App\Entity\ShoppingListItem;
use App\Enum\ShopEnum;
use App\Repository\GroceryRepository;
use App\Repository\ShoppingListItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/shopping-list')]
final class ShoppingListController extends AbstractController
{
    #[Route('', name: 'app_shopping_list_index', methods: ['GET'])]
    public function index(ShoppingListItemRepository $repository, GroceryRepository $groceryRepository): Response
    {
        $items = $repository->findAll();

        $shops = ShopEnum::cases();
        $shopTotals = [];
        foreach ($shops as $shop) {
            $shopTotals[$shop->value] = [
                'shop' => $shop,
                'total' => 0,
                'missing_items' => 0,
            ];
        }

        $optimalPlan = [];
        $totalOptimal = 0;

        foreach ($items as $item) {
            $grocery = $item->getGrocery();
            $prices = $grocery->getPrices();

            // Get latest price per shop for this grocery
            $latestPrices = [];
            foreach ($prices as $price) {
                $shopValue = $price->getShop()->value;
                if (!isset($latestPrices[$shopValue]) || $price->getCreatedAt() > $latestPrices[$shopValue]->getCreatedAt()) {
                    $latestPrices[$shopValue] = $price;
                }
            }

            // Update shop totals
            foreach ($shops as $shop) {
                if (isset($latestPrices[$shop->value])) {
                    $shopTotals[$shop->value]['total'] += (float)$latestPrices[$shop->value]->getValue() * $item->getQuantity();
                } else {
                    $shopTotals[$shop->value]['missing_items']++;
                }
            }

            // Find optimal price for this item
            $bestPrice = null;
            $bestShop = null;
            foreach ($latestPrices as $price) {
                $val = (float)$price->getValue();
                if ($bestPrice === null || $val < $bestPrice) {
                    $bestPrice = $val;
                    $bestShop = $price->getShop();
                }
            }

            if ($bestPrice !== null) {
                $itemTotal = $bestPrice * $item->getQuantity();
                $optimalPlan[] = [
                    'item' => $item,
                    'best_price' => $bestPrice,
                    'best_shop' => $bestShop,
                    'total' => $itemTotal,
                ];
                $totalOptimal += $itemTotal;
            } else {
                $optimalPlan[] = [
                    'item' => $item,
                    'best_price' => null,
                    'best_shop' => null,
                    'total' => 0,
                ];
            }
        }

        return $this->render('shopping_list/index.html.twig', [
            'items' => $items,
            'shopTotals' => $shopTotals,
            'optimalPlan' => $optimalPlan,
            'totalOptimal' => $totalOptimal,
        ]);
    }

    #[Route('/add/{id}', name: 'app_shopping_list_add', methods: ['POST'])]
    public function add(Grocery $grocery, ShoppingListItemRepository $repository, EntityManagerInterface $entityManager): Response
    {
        $item = $repository->findOneBy(['grocery' => $grocery]);
        if (!$item) {
            $item = new ShoppingListItem();
            $item->setGrocery($grocery);
            $item->setQuantity(1);
            $entityManager->persist($item);
        } else {
            $item->setQuantity($item->getQuantity() + 1);
        }

        $entityManager->flush();

        $this->addFlash('success', sprintf('%s added to shopping list.', $grocery->getName()));

        return $this->redirectToRoute('app_index');
    }

    #[Route('/remove/{id}', name: 'app_shopping_list_remove', methods: ['POST'])]
    public function remove(ShoppingListItem $item, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($item);
        $entityManager->flush();

        $this->addFlash('success', 'Item removed from shopping list.');

        return $this->redirectToRoute('app_shopping_list_index');
    }

    #[Route('/update-quantity/{id}', name: 'app_shopping_list_update_quantity', methods: ['POST'])]
    public function updateQuantity(ShoppingListItem $item, Request $request, EntityManagerInterface $entityManager): Response
    {
        $quantity = (int)$request->request->get('quantity', 1);
        if ($quantity <= 0) {
            $entityManager->remove($item);
        } else {
            $item->setQuantity($quantity);
        }
        $entityManager->flush();

        return $this->redirectToRoute('app_shopping_list_index');
    }

    #[Route('/toggle-check/{id}', name: 'app_shopping_list_toggle_check', methods: ['POST'])]
    public function toggleCheck(ShoppingListItem $item, EntityManagerInterface $entityManager): Response
    {
        $item->setChecked(!$item->isChecked());
        $entityManager->flush();

        return $this->redirectToRoute('app_shopping_list_index');
    }
}
