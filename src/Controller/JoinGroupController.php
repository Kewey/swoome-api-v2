<?php
// api/src/Controller/CreateBookPublication.php

namespace App\Controller;

use App\Entity\Balance;
use App\Factory\JsonResponseFactory;
use App\Entity\Group;
use App\Repository\BalanceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use ExpoSDK\ExpoMessage;
use ExpoSDK\Expo;

class JoinGroupController extends AbstractController
{
    public function __construct(
        EntityManagerInterface $entityManager,
        JsonResponseFactory $jsonResponseFactory,
        BalanceRepository $balanceRepository
    ) {
        $this->entityManager = $entityManager;
        $this->jsonResponseFactory = $jsonResponseFactory;
        $this->balanceRepository = $balanceRepository;
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



        $recipientsPush = [];
        foreach ($group->getMembers() as $member) {
            if ($member->getPushToken()) {
                $recipientsPush[] = $member->getPushToken();
            }
        }

        if ($recipientsPush) {
            $message = new ExpoMessage([
                'title' => $user->getUsername() . ' a rejoint ' . $group->getName(),
                'body' => 'Sera-t-il le sauveur ou le gouffre financier du groupe ?',
            ]);
            (new Expo)->send($message)->to($recipientsPush)->push();
        }


        $user->addGroup($group);

        if (!$this->balanceRepository->findBalanceByUserByGroup($user, $group)) {
            $balance = new Balance;
            $balance->setBalanceGroup($group);
            $user->addBalance($balance);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->jsonResponseFactory->create($group);
    }
}
