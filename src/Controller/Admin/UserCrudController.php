<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use Vich\UploaderBundle\Form\Type\VichImageType; 

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield EmailField::new('email'); 
        yield TextField::new('password'); 
        yield TextField::new('nom'); 
        yield TextField::new('prenom'); 
        yield TextField::new('adresse'); 
        yield TextField::new('telephone'); 
        yield DateField::new('dateNaissance'); 
        yield TextField::new('pseudo'); 
        yield TextareaField::new('imageFile')
            ->setFormType(VichImageType::class)->hideOnIndex()
            ->setLabel('Image')
            ->setRequired(false);
        yield ImageField::new('image')
            ->setBasePath('/images/users')
            ->hideOnForm();
    }
}