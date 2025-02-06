<?php

namespace App\Controller\Admin;

use App\Entity\Covoiturage;
use Doctrine\DBAL\Types\FloatType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TimeField;
use Symfony\Component\Config\Definition\FloatNode;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class CovoiturageCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Covoiturage::class;
    }


    public function configureFields(string $pageName): iterable
    {
        yield DateTimeField::new('dateDepart');
        yield TimeField::new('heureDepart');
        yield TextField::new('lieuDepart');
        yield DateTimeField::new('dateArrivee');
        yield TimeField::new('heureArrivee');
        yield TextField::new('lieuArrivee');
        yield ChoiceField::new('statut')
        ->setChoices([
            'En attente' => 'En attente',
            'En cours' => 'En cours',
            'Terminé' => 'Terminé',
        ]);
        yield IntegerField::new('nbPlace');
        yield MoneyField::new('prixPersonne')->setStoredAsCents(false)->setCustomOption('suffix', 'Crédit');
        yield AssociationField::new('user');
        yield AssociationField::new('voiture');
        
    }
    
}
