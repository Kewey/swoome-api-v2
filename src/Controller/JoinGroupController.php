<?php
// api/src/Controller/CreateBookPublication.php

namespace App\Controller;

use App\Entity\Balance;
use App\Factory\JsonResponseFactory;
use App\Entity\Group;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

class JoinGroupController extends AbstractController
{
    public function __construct(
        EntityManagerInterface $entityManager,
        JsonResponseFactory $jsonResponseFactory
    ) {
        $this->entityManager = $entityManager;
        $this->jsonResponseFactory = $jsonResponseFactory;
    }
    /**
     * @Route("/api/join_group", methods={"POST"}, name="join_group")
     * @param Request $request
     * @return Response
     */
    public function __invoke(Request $request): Response
    {
        $parameters = json_decode($request->getContent(), true);
        $code = $parameters['code'];

        if (!$code) {
            throw new BadRequestHttpException('un "code" est requis');
        }

        $groupRepository = $this->entityManager->getRepository(Group::class);
        $group = $groupRepository->findOneBy(['code' => $code]);

        if (!$group) {
            throw new BadRequestHttpException('ce "code" n\'existe pas');
        }


        /** @var User $user */
        $user = $this->getUser();

        if (in_array($user, $group->getMembers())) {
            throw new BadRequestHttpException('Vous appartenez déjà à ce groupe');
        }

        $user->addGroup($group);

        $balance = new Balance;
        $balance->setValue(0);
        $balance->setBalanceGroup($group);
        $user->addBalance($balance);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->jsonResponseFactory->create($group);
    }
}
