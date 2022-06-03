<?php

namespace App\Controller\Admin;

use App\Entity\ExpenseType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ExpenseTypeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ExpenseType::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name'),
            TextField::new('emoji'),
            BooleanField::new('isDefault', "Type dispo par défaut (fixture)"),
            AssociationField::new('groupTypes'),
            AssociationField::new('groups'),
        ];
    }
}
