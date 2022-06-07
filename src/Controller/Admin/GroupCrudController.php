<?php

namespace App\Controller\Admin;

use App\Entity\Group;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Hashids\Hashids;

class GroupCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Group::class;
    }

    public function __construct(
        EntityManagerInterface $entityManager,
    ) {
        $this->entityManager = $entityManager;
    }

    public function createEntity(string $entityFqcn)
    {
        $group = new Group();

        $lastGroupId = 0;
        $lastGroup = $this->entityManager->getRepository(Group::class)->findOneBy(array(), array('id' => 'DESC'), 1, 0);
        if ($lastGroup) {
            $lastGroupId = $lastGroup->getId();
        }
        $hashid = new Hashids("Groups", 6);
        $group->setCode(strtoupper($hashid->encode($lastGroupId + 1)));

        return $group;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name', 'Nom du groupe')->setRequired(true),
            TextField::new('code', 'Code')->hideOnForm()->setRequired(true),
            AssociationField::new('type', 'Type de groupe')->setRequired(true),
            AssociationField::new('members', 'Membres')->setRequired(true)
        ];
    }
}
