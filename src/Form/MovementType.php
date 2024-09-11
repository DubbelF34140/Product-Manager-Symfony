<?php

namespace App\Form;

use App\Entity\Movement;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MovementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Entrée' => 'Entrée',
                    'Sortie' => 'Sortie',
                    'SAV' => 'SAV',
                    'Poubelle' => 'Poubelle',
                    'Réparation' => 'Réparation',
                ],
                'expanded' => true,
            ])
            ->add('date', DateTimeType::class, [
                'widget' => 'single_text',
                'data' => new \DateTime()
            ])
            ->add('products', EntityType::class, [
                'class' => Product::class,
                'choice_label' => function (Product $product) {
                    return $product->getProductType()->getName() . ' (S/N: ' . $product->getSerialNumber() . ')';
                },
                'multiple' => true,
                'expanded' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Movement::class,
        ]);
    }
}
