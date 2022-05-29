<?php
// src/Controller/GroupTypeFixturesController.php
namespace App\Controller;

use App\Entity\GroupType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class GroupTypeFixturesController extends AbstractController
{

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
    ) {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/load-fixtures", name="loadFixtures")
     */
    public function loadFixtures()
    {
        $type1 = new GroupType();
        $type1->setName('Colocation');
        $type1->setEmoji('🏠');
        $this->entityManager->persist($type1);

        $type2 = new GroupType();
        $type2->setName('Vie en couple');
        $type2->setEmoji('👩‍❤️‍👨');
        $this->entityManager->persist($type2);

        $type3 = new GroupType();
        $type3->setName('Voyage');
        $type3->setEmoji('✈');
        $this->entityManager->persist($type3);

        $type4 = new GroupType();
        $type4->setName('Evenement');
        $type4->setEmoji('🎉');
        $this->entityManager->persist($type4);


        $type5 = new GroupType();
        $type5->setName('Projet');
        $type5->setEmoji('💻');
        $this->entityManager->persist($type5);

        $type5 = new GroupType();
        $type5->setName('Autre');
        $type5->setEmoji('🆕');
        $this->entityManager->persist($type5);

        $this->entityManager->flush();

        return $this->json([
            'message' => 'Ok'
        ]);
    }
}
