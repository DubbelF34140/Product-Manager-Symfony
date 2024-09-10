<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\ProductType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProducFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('serialNumber', TextType::class, [
                'label' => 'Numéro de série',
                'required' => true,
            ])
            ->add('productType', EntityType::class, [
                'class' => ProductType::class,
                'choice_label' => 'name',
                'label' => 'Type de produit',
                'placeholder' => 'Sélectionnez un type de produit',
                'required' => true,  // Assure que ce champ est obligatoire
            ])
            ->add('comment', TextType::class, [
                'label' => 'Commentaire',
                'required' => false,
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Entrée' => 'Entrée',
                    'Sortie' => 'Sortie',
                    'SAV' => 'SAV',
                    'Réparation' => 'Réparation',
                ],
                'required' => false,
                'expanded' => true,
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
