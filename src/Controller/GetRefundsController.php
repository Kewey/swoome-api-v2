<?php

namespace App\Controller;

use App\Entity\Group;
use App\Repository\BalanceRepository;
use App\Repository\GroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class GetRefundsController
 */
final class GetRefundsController extends AbstractController
{

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        GroupRepository $groupRepository,
        BalanceRepository $balanceRepository

    ) {
        $this->entityManager = $entityManager;
        $this->groupRepository = $groupRepository;
        $this->balanceRepository = $balanceRepository;
    }

    /**
     * @Route("/api/groups/{id}/get_refunds", name="get_refunds")
     */
    public function __invoke($id, Request $request): Response
    {
        /**
         * @var Group $group
         */
        $group = $this->groupRepository->find($id);

        if (!$group) {
            throw new BadRequestHttpException('Aucun groupe trouvé');
        }


        $balances = [];
        $members = $group->getMembers();
        foreach ($members as $member) {
            $balances[$member->getUsername()] = $this->balanceRepository->findLastBalance($member, $group);
        }

        sort($balances);


        $refunds = [];
        foreach ($balances as $key => $balance) {
        }

        return 'test';
    }
}
