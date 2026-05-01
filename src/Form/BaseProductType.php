<?php

namespace App\Form;

use App\Entity\BaseProduct;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class BaseProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => ['placeholder' => 'Base Product Name'],
                'constraints' => [
                    new Assert\NotBlank(message: 'Name cannot be blank'),
                    new Assert\Length(
                        min: 2,
                        max: 255,
                        minMessage: 'Name must be at least {{ limit }} characters',
                        maxMessage: 'Name cannot exceed {{ limit }} characters',
                    ),
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BaseProduct::class,
        ]);
    }
}
