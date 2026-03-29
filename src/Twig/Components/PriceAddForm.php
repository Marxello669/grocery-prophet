<?php

namespace App\Twig\Components;

use App\Entity\Grocery;
use App\Entity\Price;
use App\Enum\ShopEnum;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
class PriceAddForm extends AbstractController
{
    use DefaultActionTrait;

    #[Assert\Positive]
    #[LiveProp(writable: true)]
    public float $price = 0;

    #[LiveProp]
    public Grocery|null $grocery = null;

    #[LiveProp(writable: true)]
    public ShopEnum $shop;

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ValidatorInterface     $validator
    )
    {
    }

    #[LiveAction]
    public function save(): void
    {
        $this->validator->validate($this->price);
        $this->validator->validate($this->grocery);
        $this->validator->validate($this->shop);

        $price = new Price();
        $price->setGrocery($this->grocery);
        $price->setShop($this->shop);
        $price->setCreatedAt(new DateTimeImmutable());

        $this->em->persist($price);
        $this->em->flush();
    }
}
