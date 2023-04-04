<?php

namespace App\Controller\Admin;

use App\Entity\Sortie;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class SortieCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Sortie::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            AssociationField::new('organisateur'),
            AssociationField::new('campus'),
            AssociationField::new('lieu'),
            AssociationField::new('etat'),
            TextField::new('nom'),
            DateTimeField::new('datedebut'),
            NumberField::new('duree'),
            DateTimeField::new('datecloture'),
            NumberField::new('nbinscriptionsmax'),
            TextEditorField::new('descriptioninfos'),
        ];
    }

}
