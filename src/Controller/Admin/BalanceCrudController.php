<?php

namespace App\Controller\Admin;

use App\Entity\Balance;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;

class BalanceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Balance::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            MoneyField::new('price', 'Prix')->setCurrency('EUR')->setStoredAsCents(true)->setRequired(true),
            AssociationField::new('balanceUser', 'Utilisateur'),
            AssociationField::new('balanceGroup', 'Groupe'),
        ];
    }
}
