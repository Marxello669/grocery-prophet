<?php

namespace App\Form;

use App\Entity\Grocery;
use App\Enum\GroceryEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GroceryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => ['placeholder' => 'Grocery Name']
            ])
            ->add('type', EnumType::class, [
                'class' => GroceryEnum::class,
                'choice_label' => 'label',
                'placeholder' => 'Grocery Type'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Grocery::class,
        ]);
    }
}
