<?php

namespace App\DataFixtures;

use App\Entity\ExpenseType;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function __construct(
        UserRepository $userRepository,
    ) {
        $this->userRepository = $userRepository;
    }

    public function load(ObjectManager $manager): void
    {
        $tom = $this->userRepository->findOneByEmail('tom.siatka@laposte.net');

        if ($tom) {
            $tom->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
            $manager->persist($tom);
        }

        $jordan = $this->userRepository->findOneByEmail('jordan-souchez@hotmail.fr');

        if ($jordan) {
            $jordan->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
            $manager->persist($jordan);
        }

        $manager->flush();
    }
}
