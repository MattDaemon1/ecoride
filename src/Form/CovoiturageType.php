<?php

namespace App\Form;

use App\Entity\Covoiturage;
use App\Entity\Voiture;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Marque;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;

class CovoiturageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateDepart', DateType::class, [
                'label' => 'Date de départ',
            ])
            ->add('heureDepart', TimeType::class, [
                'label' => 'Heure de départ',
            ])
            ->add('lieuDepart', TextType::class, [
                'label' => 'Lieu de départ',
            ])
            ->add('dateArrivee', DateType::class, [
                'label' => 'Date d\'arrivée',
            ])
            ->add('heureArrivee', TimeType::class, [
                'label' => 'Heure d\'arrivée',
            ])
            ->add('lieuArrivee', TextType::class, [
                'label' => 'Lieu d\'arrivée',
            ])
            ->add('statut', ChoiceType::class, [
                'choices' => [
                    'En attente' => 'En attente',
                    'En cours' => 'En cours',
                    'Terminé' => 'Terminé',
                ],
            ])
            ->add('nbPlace', IntegerType::class, [
                'label' => 'Nombre de places',
            ])
            ->add('prixPersonne', IntegerType::class, [
                'label' => 'Prix par personne',
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'email',
            ])
            ->add('voiture', EntityType::class, [
                'class' => Voiture::class,
                'label' => 'Véhicule',
                'choice_label' => 'immatriculation', // Afficher l'immatriculation comme label
                'query_builder' => function (EntityRepository $er) use ($options) {
                    $user = $options['user'];
                    return $er->createQueryBuilder('v')
                        ->where('v.user = :user')
                        ->setParameter('user', $user);
                },
                'placeholder' => 'Sélectionnez un véhicule',
                'required' => true,
            ]);
        
            
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Covoiturage::class,
            'user' => null, // Option personnalisée
        ]);
    }
}