<?php

// src/Form/SearchCovoiturageType.php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchCovoiturageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lieuDepart', TextType::class, [
                'label' => 'Lieu de départ',
                'attr' => [
                    'placeholder' => 'Ville de départ',
                    'class' => 'form-control'
                ],
                'required' => true
            ])
            ->add('lieuArrivee', TextType::class, [
                'label' => 'Lieu d\'arrivée',
                'attr' => [
                    'placeholder' => 'Ville d\'arrivée',
                    'class' => 'form-control'
                ],
                'required' => true
            ])
            ->add('dateDepart', DateType::class, [
                'label' => 'Date de départ',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control'
                ],
                'required' => true,
                'data' => new \DateTime() // Date du jour par défaut
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'method' => 'GET',
            'csrf_protection' => false // Désactive CSRF pour permettre le partage des liens de recherche
        ]);
    }
}