<?php

namespace App\Controller\Admin;

use App\Entity\Covoiturage;
use Doctrine\DBAL\Types\FloatType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Config\Definition\FloatNode;

class CovoiturageCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Covoiturage::class;
    }


    public function configureFields(string $pageName): iterable
    {
        yield DateTimeField::new('dateDepart');
        yield DateTimeField::new('heureDepart');
        yield TextField::new('lieuDepart');
        yield DateTimeField::new('dateArrivee');
        yield DateTimeField::new('heureArrivee');
        yield TextField::new('lieuArrivee');
        yield TextField::new('statut');
        yield IntegerField::new('nbPlace');
        yield MoneyField::new('prixPersonne')->setCurrency('Crédit')->setStoredAsCents(false);
        yield AssociationField::new('user');
        yield AssociationField::new('voiture');
        
    }
    
}
