<?php

namespace App\Form;

use App\Entity\BaseProduct;
use App\Entity\Grocery;
use App\Enum\GroceryEnum;
use App\Enum\UnitEnum;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class GroceryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => ['placeholder' => 'Grocery Name'],
                'constraints' => [
                    new Assert\NotBlank(message: 'Name cannot be blank'),
                    new Assert\Length(
                        min:2,
                        max: 255,
                        minMessage: 'Name must be at least {{ limit }} characters',
                        maxMessage: 'Name cannot exceed {{ limit }} characters',
                    ),
                ]
            ])
            ->add('type', EnumType::class, [
                'class' => GroceryEnum::class,
                'choice_label' => 'label',
                'placeholder' => 'Chose a grocery type',
                'constraints' => [new Assert\NotNull()]
            ])
            ->add('unit', EnumType::class, [
                'class' => UnitEnum::class,
                'choice_label' => 'label',
                'placeholder' => 'Chose a unit',
                'constraints' => [new Assert\NotNull()]
            ])
            ->add('baseProduct', EntityType::class, [
                'class' => BaseProduct::class,
                'choice_label' => 'name',
                'placeholder' => 'Select a base product (optional)',
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Grocery::class,
        ]);
    }
}
