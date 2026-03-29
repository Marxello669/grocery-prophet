<?php

namespace App\Controller;

use App\Entity\Grocery;
use App\Form\GroceryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class IndexController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $grocery = new Grocery();
        $form = $this->createForm(GroceryType::class, $grocery);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($grocery);
            $entityManager->flush();

            return $this->redirectToRoute('app_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('index/index.html.twig', [
            'grocery_form' => $form->createView(),
        ]);
    }
}
