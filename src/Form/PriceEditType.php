<?php

namespace App\Form;

use App\Entity\Grocery;
use App\Entity\Price;
use App\Enum\ShopEnum;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PriceEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('value')
            ->add('shop', EnumType::class, ['class' => ShopEnum::class, 'choice_label' => 'label', 'placeholder' => 'Choose a shop'])
            ->add('grocery', EntityType::class, ['class' => Grocery::class, 'choice_label' => 'name', 'placeholder' => 'Choose a grocery'])
            ->add('createdAt', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date',
                'required' => false,
                'input' => 'datetime_immutable',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Price::class,
        ]);
    }
}
