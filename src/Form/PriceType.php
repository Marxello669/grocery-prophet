<?php

namespace App\Form;

use App\DTO\Price\NewDTO;
use App\Entity\Grocery;
use App\Enum\ShopEnum;
use App\Enum\UnitEnum;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfonycasts\DynamicForms\DependentField;
use Symfonycasts\DynamicForms\DynamicFormBuilder;

class PriceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder = new DynamicFormBuilder($builder);

        $builder
            ->add('value', NumberType::class)
            ->add('shop', EnumType::class, [
                'class' => ShopEnum::class,
                'choice_label' => 'label',
                'placeholder' => 'Selecione a loja'
            ])
            ->add('grocery', GroceryAutoCompleteType::class)
            ->add('quantity', NumberType::class)
            ->addDependent('subUnit', 'grocery', function (DependentField $field, ?Grocery $grocery) {
                $field->add(EnumType::class, [
                    'class' => UnitEnum::class,
                    'choices' => $grocery?->getUnit()?->getSubunits() ?? UnitEnum::cases(),
                    'placeholder' => 'Selecione a unidade da quantidade introduzida',
                ]);
            });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => NewDTO::class,
        ]);
    }
}
