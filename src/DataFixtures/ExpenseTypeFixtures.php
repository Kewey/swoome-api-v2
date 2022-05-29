<?php

namespace App\DataFixtures;

use App\Entity\ExpenseType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ExpenseTypeFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $type1 = new ExpenseType();
        $type1->setName('Autres');
        $type1->setEmoji('🏷');
        $manager->persist($type1);

        $type2 = new ExpenseType();
        $type2->setName('Alimentation');
        $type2->setEmoji('🍕');
        $manager->persist($type2);

        $type3 = new ExpenseType();
        $type3->setName('Musée');
        $type3->setEmoji('🎨');
        $manager->persist($type3);

        $type4 = new ExpenseType();
        $type4->setName('Restaurant & bar');
        $type4->setEmoji('🍹');
        $manager->persist($type4);

        $type5 = new ExpenseType();
        $type5->setName('Shopping');
        $type5->setEmoji('🛒');
        $manager->persist($type5);

        $type6 = new ExpenseType();
        $type6->setName('Transport');
        $type6->setEmoji('🚗');
        $manager->persist($type6);

        $manager->flush();
    }
}
