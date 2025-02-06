<?php

namespace App\Form;

use App\Entity\Covoiturage;
use App\Entity\Voiture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Marque;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class VoitureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('immatriculation', TextType::class, [
                'label' => 'Plaque d\'immatriculation',
            ])
            ->add('datePremiereImmatriculation', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de première immatriculation',
            ])
            ->add('modele', TextType::class, [
                'label' => 'Modèle',
            ])
            ->add('marque', EntityType::class, [
                'class' => Marque::class,
                'label' => 'Marque',
            ])
            ->add('energie', ChoiceType::class, [
                'label' => 'Energie',
                'choices' => [
                    'Electrique' => 'Électrique',
                    'Essence' => 'Essence',
                    'Diesel' => 'Diesel',
                    'Autre' => 'Autre',
                    
                ],
                
                
            ])
            ->add('couleur', TextType::class, [
                'label' => 'Couleur',
            ]);
         
    }

    
}
