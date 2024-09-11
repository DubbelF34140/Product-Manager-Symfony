<?php

namespace App\Form;

use App\Entity\Brand;
use App\Entity\Category;
use App\Entity\ProductType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProducTypeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du type de produit',
            ])
            ->add('brand', EntityType::class, [
                'class' => Brand::class,
                'choice_label' => 'name',
                'label' => 'Marque',
                'placeholder' => 'Sélectionner une marque',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'label' => 'Catégorie',
                'placeholder' => 'Sélectionner une catégorie',
                'attr' => ['class' => 'form-control'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProductType::class,
        ]);
    }
}
