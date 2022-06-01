<?php
// src/DataPersister/ExpenseDataPersister.php

namespace App\DataPersister;

use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Balance;
use App\Entity\Expense;
use App\Entity\Refund;
use App\Repository\BalanceRepository;
use App\Repository\ExpenseRepository;
use DateTimeImmutable;
use Symfony\Component\Security\Core\Security;

/**
 *
 */
class ExpenseDataPersister implements ContextAwareDataPersisterInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    private $security;

    public function __construct(
        EntityManagerInterface $entityManager,
        Security $security,
        BalanceRepository $balanceRepository,
        ExpenseRepository $expenseRepository
    ) {
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->balanceRepository = $balanceRepository;
        $this->expenseRepository = $expenseRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($data, array $context = []): bool
    {
        return $data instanceof Expense;
    }

    /**
     * @param Expense $data
     */
    public function persist($data, array $context = []): void
    {
        $currentUser = $this->security->getUser();

        if (!$data->getMadeBy()) {
            $data->setMadeBy($currentUser);
        }

        if (!$data->getExpenseAt()) {
            $data->setExpenseAt(new DateTimeImmutable());
        }

        $this->removeRefunds($data);
        if (
            ($context['collection_operation_name'] ?? null) === 'post' ||
            ($context['graphql_operation_name'] ?? null) === 'create'
        ) {
            $this->calculateBalances($data);
        }

        $this->entityManager->persist($data);

        if (
            ($context['item_operation_name'] ?? null) === 'put' ||
            ($context['graphql_operation_name'] ?? null) === 'edit'
        ) {
            $this->calculateAllBalances($data);
        }

        $this->entityManager->flush();
    }

    public function removeRefunds($data)
    {
        $oldRefunds = $data->getExpenseGroup()->getRefunds();
        foreach ($oldRefunds as $oldRefund) {
            $this->entityManager->remove($oldRefund);
        }
    }


    public function calculateAllBalances($data)
    {
        $balances = [];
        foreach ($data->getExpenseGroup()->getMembers() as $user) {
            $balanceValue = 0;
            foreach ($this->expenseRepository->findExpenseByUserAndGroup($user, $data->getExpenseGroup()) as $expense) {
                if ($user == $expense->getMadeBy()) {
                    $balanceValue += $expense->getPrice() - ($expense->getPrice() / $expense->getParticipants()->count());
                } else {
                    $balanceValue += - ($expense->getPrice() / $expense->getParticipants()->count());
                }
            }
            $lastBalance = $this->balanceRepository->findBalanceByUserByGroup($user, $data->getExpenseGroup());
            $lastBalance->setValue($balanceValue);
            $this->entityManager->persist($lastBalance);
            $balances[] = clone $lastBalance;
        }
        $this->calculateRefunds($balances);
    }

    public function calculateBalances($data)
    {
        $balances = [];
        foreach ($data->getParticipants() as $user) {
            $this->entityManager->persist($user);
            if ($user == $data->getMadeBy()) {
                $balanceValue = $data->getPrice() - ($data->getPrice() / $data->getParticipants()->count());
            } else {
                $balanceValue = - ($data->getPrice() / $data->getParticipants()->count());
            }

            $lastBalance = $this->balanceRepository->findBalanceByUserByGroup($user, $data->getExpenseGroup());
            if (!$lastBalance) {
                $balance = new Balance;
                $balance->setBalanceUser($user);
                $balance->setBalanceGroup($data->getExpenseGroup());
                $balance->setValue($balanceValue);
                $this->entityManager->persist($balance);
                $balances[] = clone $balance;
            } else {
                $lastBalance->setValue($lastBalance->getValue() + $balanceValue);
                $this->entityManager->persist($lastBalance);
                $balances[] = clone $lastBalance;
            }
        }
        $this->calculateRefunds($balances);
    }


    public function calculateRefunds($balances)
    {
        if ($balances) {
            $positiveBalances = [];
            $negativeBalances = [];
            foreach ($balances as $balance) {
                if ($balance->getValue() > 0) {
                    $positiveBalances[] = $balance;
                }
                if ($balance->getValue() < 0) {
                    $negativeBalances[] = $balance;
                }
            }

            rsort($positiveBalances);
            sort($negativeBalances);

            foreach ($positiveBalances as $positiveBalance) {
                foreach ($negativeBalances as $negativeBalance) {
                    if ($positiveBalance->getValue() > 0 && $negativeBalance->getValue() < 0) {
                        if ($positiveBalance->getValue() >= - ($negativeBalance->getValue())) {
                            $newPositiveBalance = $positiveBalance->getValue() + $negativeBalance->getValue();
                            $newNegativeBalance = $negativeBalance->getValue() + (-$negativeBalance->getValue());
                        } else {
                            $newPositiveBalance = 0;
                            $newNegativeBalance = $negativeBalance->getValue() + $positiveBalance->getValue();
                        }
                        $refund = new Refund();
                        $refund->setRefundGroup($positiveBalance->getBalanceGroup());
                        $refund->setPrice($newNegativeBalance - $negativeBalance->getValue());
                        $refund->setRefunder($negativeBalance->getBalanceUser());
                        $refund->setReceiver($positiveBalance->getBalanceUser());

                        $this->entityManager->persist($refund);

                        $positiveBalance->setValue($newPositiveBalance);
                        $negativeBalance->setValue($newNegativeBalance);
                    }
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function remove($data, array $context = [])
    {
        $this->entityManager->remove($data);
        $this->calculateAllBalances($data);
        $this->entityManager->flush();
    }
}
