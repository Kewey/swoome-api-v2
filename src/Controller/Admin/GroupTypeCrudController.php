<?php

namespace App\Controller\Admin;

use App\Entity\GroupType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class GroupTypeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return GroupType::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular(
                fn (?GroupType $groupType, ?string $pageName) => $groupType ? $groupType->__toString() : 'Catégorie de groupe'
            )
            ->setEntityLabelInPlural(function (?GroupType $groupType, ?string $pageName) {
                return 'edit' === $pageName ? $groupType->__toString() : 'Catégories de groupe';
            });
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name'),
            TextField::new('emoji'),
            AssociationField::new('expenseTypes'),
            AssociationField::new('groups')
        ];
    }
}
