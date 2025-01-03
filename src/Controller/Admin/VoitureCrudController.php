<?php

namespace App\Controller\Admin;

use App\Entity\Voiture;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class VoitureCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Voiture::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('modele');
        yield TextField::new('immatriculation');
        yield TextField::new('couleur');
        yield ChoiceField::new('energie')
        ->setChoices([
            'Électrique' => 'Électrique',
            'Essence' => 'Essence',
            'Diesel' => 'Diesel',
            'Autre' => 'Autre',
        ]);
        yield DateTimeField::new('datePremiereImmatriculation');
        yield AssociationField::new('marque');
        yield AssociationField::new('user'); 
    }
    
}