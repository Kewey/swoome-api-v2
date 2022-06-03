<?php

namespace App\DataFixtures;

use App\Entity\GroupType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class GroupTypeFixtures extends Fixture
{
    public const COLOC_GROUPTYPE_REFERENCE = 'coloc-grouptype';
    public const COUPLE_GROUPTYPE_REFERENCE = 'couple-grouptype';
    public const VOYAGE_GROUPTYPE_REFERENCE = 'voyage-grouptype';
    public const EVENT_GROUPTYPE_REFERENCE = 'event-grouptype';
    public const PROJECT_GROUPTYPE_REFERENCE = 'project-grouptype';
    public const OTHER_GROUPTYPE_REFERENCE = 'other-grouptype';

    public function load(ObjectManager $manager): void
    {
        $coloc = new GroupType();
        $coloc->setName('Colocation');
        $coloc->setEmoji('🏠');
        $manager->persist($coloc);

        $couple = new GroupType();
        $couple->setName('Vie en couple');
        $couple->setEmoji('👩‍❤️‍👨');
        $manager->persist($couple);

        $voyage = new GroupType();
        $voyage->setName('Voyage');
        $voyage->setEmoji('✈');
        $manager->persist($voyage);

        $event = new GroupType();
        $event->setName('Evenement');
        $event->setEmoji('🎉');
        $manager->persist($event);

        $project = new GroupType();
        $project->setName('Projet');
        $project->setEmoji('💻');
        $manager->persist($project);

        $other = new GroupType();
        $other->setName('Autre');
        $other->setEmoji('🏷');
        $manager->persist($other);

        $manager->flush();

        $this->addReference(self::COLOC_GROUPTYPE_REFERENCE, $coloc);
        $this->addReference(self::COUPLE_GROUPTYPE_REFERENCE, $couple);
        $this->addReference(self::VOYAGE_GROUPTYPE_REFERENCE, $voyage);
        $this->addReference(self::EVENT_GROUPTYPE_REFERENCE, $event);
        $this->addReference(self::PROJECT_GROUPTYPE_REFERENCE, $project);
        $this->addReference(self::OTHER_GROUPTYPE_REFERENCE, $other);
    }
}
