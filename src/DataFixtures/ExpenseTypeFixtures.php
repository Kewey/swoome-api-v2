<?php

namespace App\DataFixtures;

use App\Entity\ExpenseType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ExpenseTypeFixtures extends Fixture  implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $type2 = new ExpenseType();
        $type2->setName('Alimentation');
        $type2->setEmoji('🍕');
        $type2->setIsDefault(true);
        $type2->addGroupType($this->getReference(GroupTypeFixtures::COLOC_GROUPTYPE_REFERENCE));
        $type2->addGroupType($this->getReference(GroupTypeFixtures::COUPLE_GROUPTYPE_REFERENCE));
        $type2->addGroupType($this->getReference(GroupTypeFixtures::VOYAGE_GROUPTYPE_REFERENCE));
        $type2->addGroupType($this->getReference(GroupTypeFixtures::EVENT_GROUPTYPE_REFERENCE));
        $type2->addGroupType($this->getReference(GroupTypeFixtures::PROJECT_GROUPTYPE_REFERENCE));
        $type2->addGroupType($this->getReference(GroupTypeFixtures::OTHER_GROUPTYPE_REFERENCE));
        $manager->persist($type2);

        $type3 = new ExpenseType();
        $type3->setName('Musée');
        $type3->setEmoji('🎨');
        $type3->setIsDefault(true);
        $type3->addGroupType($this->getReference(GroupTypeFixtures::COUPLE_GROUPTYPE_REFERENCE));
        $type3->addGroupType($this->getReference(GroupTypeFixtures::VOYAGE_GROUPTYPE_REFERENCE));
        $type3->addGroupType($this->getReference(GroupTypeFixtures::EVENT_GROUPTYPE_REFERENCE));
        $type3->addGroupType($this->getReference(GroupTypeFixtures::PROJECT_GROUPTYPE_REFERENCE));
        $type3->addGroupType($this->getReference(GroupTypeFixtures::OTHER_GROUPTYPE_REFERENCE));
        $manager->persist($type3);

        $type4 = new ExpenseType();
        $type4->setName('Restaurant & bar');
        $type4->setEmoji('🍹');
        $type4->setIsDefault(true);
        $type4->addGroupType($this->getReference(GroupTypeFixtures::COLOC_GROUPTYPE_REFERENCE));
        $type4->addGroupType($this->getReference(GroupTypeFixtures::COUPLE_GROUPTYPE_REFERENCE));
        $type4->addGroupType($this->getReference(GroupTypeFixtures::VOYAGE_GROUPTYPE_REFERENCE));
        $type4->addGroupType($this->getReference(GroupTypeFixtures::EVENT_GROUPTYPE_REFERENCE));
        $type4->addGroupType($this->getReference(GroupTypeFixtures::PROJECT_GROUPTYPE_REFERENCE));
        $type4->addGroupType($this->getReference(GroupTypeFixtures::OTHER_GROUPTYPE_REFERENCE));
        $manager->persist($type4);

        $type5 = new ExpenseType();
        $type5->setName('Shopping');
        $type5->setEmoji('🛒');
        $type5->setIsDefault(true);
        $type5->addGroupType($this->getReference(GroupTypeFixtures::COLOC_GROUPTYPE_REFERENCE));
        $type5->addGroupType($this->getReference(GroupTypeFixtures::COUPLE_GROUPTYPE_REFERENCE));
        $type5->addGroupType($this->getReference(GroupTypeFixtures::VOYAGE_GROUPTYPE_REFERENCE));
        $type5->addGroupType($this->getReference(GroupTypeFixtures::EVENT_GROUPTYPE_REFERENCE));
        $type5->addGroupType($this->getReference(GroupTypeFixtures::PROJECT_GROUPTYPE_REFERENCE));
        $type5->addGroupType($this->getReference(GroupTypeFixtures::OTHER_GROUPTYPE_REFERENCE));
        $manager->persist($type5);

        $type6 = new ExpenseType();
        $type6->setName('Transport');
        $type6->setEmoji('🚗');
        $type6->setIsDefault(true);
        $type6->addGroupType($this->getReference(GroupTypeFixtures::COLOC_GROUPTYPE_REFERENCE));
        $type6->addGroupType($this->getReference(GroupTypeFixtures::COUPLE_GROUPTYPE_REFERENCE));
        $type6->addGroupType($this->getReference(GroupTypeFixtures::VOYAGE_GROUPTYPE_REFERENCE));
        $type6->addGroupType($this->getReference(GroupTypeFixtures::EVENT_GROUPTYPE_REFERENCE));
        $type6->addGroupType($this->getReference(GroupTypeFixtures::PROJECT_GROUPTYPE_REFERENCE));
        $type6->addGroupType($this->getReference(GroupTypeFixtures::OTHER_GROUPTYPE_REFERENCE));
        $manager->persist($type6);


        $type7 = new ExpenseType();
        $type7->setName('Remboursement');
        $type7->setEmoji('💸');
        $type7->setIsDefault(true);
        $type7->addGroupType($this->getReference(GroupTypeFixtures::COLOC_GROUPTYPE_REFERENCE));
        $type7->addGroupType($this->getReference(GroupTypeFixtures::COUPLE_GROUPTYPE_REFERENCE));
        $type7->addGroupType($this->getReference(GroupTypeFixtures::VOYAGE_GROUPTYPE_REFERENCE));
        $type7->addGroupType($this->getReference(GroupTypeFixtures::EVENT_GROUPTYPE_REFERENCE));
        $type7->addGroupType($this->getReference(GroupTypeFixtures::PROJECT_GROUPTYPE_REFERENCE));
        $type7->addGroupType($this->getReference(GroupTypeFixtures::OTHER_GROUPTYPE_REFERENCE));
        $manager->persist($type7);

        $type1 = new ExpenseType();
        $type1->setName('Autres');
        $type1->setEmoji('🏷');
        $type1->setIsDefault(true);
        $type1->addGroupType($this->getReference(GroupTypeFixtures::COLOC_GROUPTYPE_REFERENCE));
        $type1->addGroupType($this->getReference(GroupTypeFixtures::COUPLE_GROUPTYPE_REFERENCE));
        $type1->addGroupType($this->getReference(GroupTypeFixtures::VOYAGE_GROUPTYPE_REFERENCE));
        $type1->addGroupType($this->getReference(GroupTypeFixtures::EVENT_GROUPTYPE_REFERENCE));
        $type1->addGroupType($this->getReference(GroupTypeFixtures::PROJECT_GROUPTYPE_REFERENCE));
        $type1->addGroupType($this->getReference(GroupTypeFixtures::OTHER_GROUPTYPE_REFERENCE));
        $manager->persist($type1);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            GroupTypeFixtures::class,
        ];
    }
}
