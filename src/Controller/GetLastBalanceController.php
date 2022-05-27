<?php
// api/src/Controller/CreateBookPublication.php

namespace App\Controller;

use App\Factory\JsonResponseFactory;
use App\Entity\Group;
use App\Repository\BalanceRepository;
use App\Repository\GroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

class GetLastBalanceController extends AbstractController
{
    public function __construct(
        EntityManagerInterface $entityManager,
        JsonResponseFactory $jsonResponseFactory,
        GroupRepository $groupRepository,
        BalanceRepository $balanceRepository
    ) {
        $this->entityManager = $entityManager;
        $this->jsonResponseFactory = $jsonResponseFactory;
        $this->groupRepository = $groupRepository;
        $this->balanceRepository = $balanceRepository;
    }
    /**
     * @Route("/api/groups/{id}/balances", methods={"GET"}, name="balances")
     * @param Request $request
     * @return Response
     */
    public function __invoke($id): Response
    {
        $group = $this->groupRepository->findOneBy(['id' => $id]);

        if (!$group) {
            throw new BadRequestHttpException('ce "groupe" n\'existe pas');
        }

        $balances = [];
        $members = $group->getMembers();
        foreach ($members as $member) {
            $balances[$member->getUsername()] = $this->balanceRepository->findBalanceByUserByGroup($member, $group);
        }

        return $this->jsonResponseFactory->create($balances);
    }
}
