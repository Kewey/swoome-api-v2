<?php

namespace App\Controller\Admin;

use App\Entity\ExpenseType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ExpenseTypeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ExpenseType::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
