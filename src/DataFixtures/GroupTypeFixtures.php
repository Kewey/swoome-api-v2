<?php
// src/DataFixtures/AppFixtures.php
namespace App\DataFixtures;

use App\Entity\GroupType;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class GroupTypeFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $type1 = new GroupType();
        $type1->setName('Colocation');
        $type1->setEmoji('');
        $manager->persist($type1);

        $type2 = new GroupType();
        $type2->setName('Vie en couple');
        $type2->setEmoji('');
        $manager->persist($type2);

        $type3 = new GroupType();
        $type3->setName('Voyage');
        $type3->setEmoji('');
        $manager->persist($type3);

        $type4 = new GroupType();
        $type4->setName('Evenement');
        $type4->setEmoji('');
        $manager->persist($type4);


        $type5 = new GroupType();
        $type5->setName('Projet');
        $type5->setEmoji('');
        $manager->persist($type5);

        $type5 = new GroupType();
        $type5->setName('Autre');
        $type5->setEmoji('');
        $manager->persist($type5);

        $manager->flush();
    }
}
