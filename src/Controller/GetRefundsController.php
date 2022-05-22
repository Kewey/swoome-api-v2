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
     * @Route("/api/groups/{id}/refunds", name="get_refunds")
     */
    public function getRefunds($id): array
    {
        $refunds = [];

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

        if ($balances) {
            $positiveBalances = [];
            $negativeBalances = [];
            foreach ($balances as $balance) {
                if ($balance > 0) {
                    $positiveBalances[] = $balance;
                }
                if ($balance < 0) {
                    $negativeBalances[] = $balance;
                }

                rsort($positiveBalances);
                sort($negativeBalances);


                foreach ($positiveBalances as $positiveBalance) {
                    foreach ($negativeBalances as $negativeBalance) {
                        if ($positiveBalance > 0 && $negativeBalance < 0) {
                            if (-$negativeBalance < $positiveBalance) {
                                $newPositiveBalance = $positiveBalance + $negativeBalance;
                                $newNegativeBalance = $negativeBalance + $negativeBalance;
                            } else {
                                $newPositiveBalance = 0;
                                $newNegativeBalance = $negativeBalance + $positiveBalance;
                            }
                            $refunds[] = [$newNegativeBalance - $negativeBalance];
                            $positiveBalance = $newPositiveBalance;
                            $negativeBalance = $newNegativeBalance;
                        }
                    }
                }
            }
        }

        return $refunds;
    }
}
