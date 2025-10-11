<?php

namespace App\Form;

use App\Entity\LigneFacture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LigneFactureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('designation', TextType::class, [
                'label' => 'Désignation',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Description du produit/service'
                ]
            ])
            ->add('quantite', NumberType::class, [
                'label' => 'Quantité',
                'attr' => [
                    'class' => 'form-control',
                    'min' => 1,
                    'step' => 1
                ]
            ])
            ->add('prixUnitaire', NumberType::class, [
                'label' => 'Prix unitaire',
                'attr' => [
                    'class' => 'form-control prix-unitaire',
                    'min' => 0,
                    'step' => 0.01
                ]
            ])
            ->add('tva', NumberType::class, [
                'label' => 'TVA (%)',
                'attr' => [
                    'class' => 'form-control tva',
                    'min' => 0,
                    'max' => 100,
                    'step' => 0.01
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => LigneFacture::class,
        ]);
    }
}

