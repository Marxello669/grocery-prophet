<?php

namespace App\Form;

use App\Entity\Recipe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class RecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => ['placeholder' => 'Recipe name'],
                'constraints' => [
                    new Assert\NotBlank(message: 'Recipe name cannot be blank'),
                    new Assert\Length(
                        min: 2,
                        max: 255,
                        minMessage: 'Recipe name must be at least {{ limit }} characters',
                        maxMessage: 'Recipe name cannot exceed {{ limit }} characters',
                    ),
                ]
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Recipe description (optional)',
                    'rows' => 4
                ]
            ])
            ->add('servings', IntegerType::class, [
                'required' => false,
                'attr' => ['placeholder' => 'Number of servings (optional)'],
                'constraints' => [
                    new Assert\Positive(message: 'Servings must be a positive number')
                ]
            ])
            ->add('link', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Link to recipe (YouTube, webpage, etc.) (optional)',
                    'type' => 'url'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}
