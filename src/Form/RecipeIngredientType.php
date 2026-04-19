<?php

namespace App\Form;

use App\Entity\BaseProduct;
use App\Entity\RecipeIngredient;
use App\Enum\UnitEnum;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecipeIngredientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('baseProduct', EntityType::class, [
                'class' => BaseProduct::class,
                'choice_label' => 'name',
                'placeholder' => 'Select a base product',
                'attr' => ['class' => 'select select-bordered']
            ])
            ->add('quantity', NumberType::class, [
                'attr' => ['placeholder' => 'Quantity', 'step' => '0.01']
            ])
            ->add('unit', EnumType::class, [
                'class' => UnitEnum::class,
                'choice_label' => 'label',
                'placeholder' => 'Select unit',
                'attr' => ['class' => 'select select-bordered']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RecipeIngredient::class,
        ]);
    }
}
