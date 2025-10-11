<?php

namespace App\Form;

use App\Entity\Facture;
use App\Entity\Tiers;
use App\Form\LigneFactureType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FactureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('reference', TextType::class, [
                'label' => 'Référence',
                'attr' => [
                    'class' => 'form-control',
                    'readonly' => true
                ]
            ])
            ->add('client', EntityType::class, [
                'class' => Tiers::class,
                'choice_label' => 'nom',
                'label' => 'Client *',
                'attr' => [
                    'class' => 'form-select'
                ],
                'placeholder' => 'Sélectionner un client'
            ])
            ->add('dateFacture', DateType::class, [
                'label' => 'Date de facture *',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('dateEcheance', DateType::class, [
                'label' => 'Date d\'échéance',
                'widget' => 'single_text',
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('etat', ChoiceType::class, [
                'label' => 'État',
                'choices' => [
                    'Brouillon' => Facture::ETAT_BROUILLON,
                    'Validée' => Facture::ETAT_VALIDEE,
                    'Payée' => Facture::ETAT_PAYEE,
                ],
                'attr' => [
                    'class' => 'form-select'
                ]
            ])
            ->add('devise', ChoiceType::class, [
                'label' => 'Devise',
                'choices' => [
                    'MAD' => 'MAD',
                    'EUR' => 'EUR',
                    'USD' => 'USD',
                ],
                'attr' => [
                    'class' => 'form-select'
                ]
            ])
            ->add('notes', TextareaType::class, [
                'label' => 'Notes',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 3,
                    'placeholder' => 'Notes additionnelles'
                ]
            ])
            ->add('lignes', CollectionType::class, [
                'entry_type' => LigneFactureType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => 'Lignes de facture',
                'attr' => [
                    'class' => 'lignes-collection'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Facture::class,
        ]);
    }
}

