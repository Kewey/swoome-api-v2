<?php

namespace App\Controller\Admin;

use App\Entity\Expense;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ExpenseCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Expense::class;
    }

    public function createEntity(string $entityFqcn)
    {
        $expense = new Expense();

        $dateTime = new \DateTime();
        $dateTime->format('d-m-Y H:i:s');
        $expense->setCreatedAt($dateTime);
        return $expense;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name', 'Titre')->setRequired(true),
            MoneyField::new('price', 'Prix')->setCurrency('EUR')->setStoredAsCents(true)->setRequired(true),
            DateTimeField::new('createdAt', 'Dépense créée le')->setRequired(true),
            DateTimeField::new('expenseAt', 'Dépense faite le')->setRequired(true),
            TextareaField::new('description'),
            AssociationField::new('madeBy', 'Le sauveur')->setRequired(true),
            AssociationField::new('participants')->setRequired(true),
            AssociationField::new('expenseGroup', 'Groupe')->setRequired(true),
            AssociationField::new('type', 'Type de dépense')
        ];
    }
}
