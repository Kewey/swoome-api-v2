<?php
// src/DataPersister/ExpenseDataPersister.php

namespace App\DataPersister;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Balance;
use App\Entity\Expense;
use App\Repository\BalanceRepository;
use DateTime;
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
        BalanceRepository $balanceRepository
    ) {
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->balanceRepository = $balanceRepository;
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

        foreach ($data->getParticipants() as $user) {
            $this->entityManager->persist($user);
            $balance = new Balance;
            $balance->setBalanceUser($user);
            $balance->setExpense($data);
            if ($user == $data->getMadeBy()) {
                $balanceValue = $data->getPrice() / $data->getParticipants()->count();
            } else {
                $balanceValue = - ($data->getPrice() / $data->getParticipants()->count());
            }
            $lastBalance = $this->balanceRepository->findLastBalance($user, $data->getExpenseGroup());
            if ($lastBalance) {
                $balanceValue += $lastBalance->getValue();
            }
            $balance->setValue($balanceValue);
            $this->entityManager->persist($balance);
        }

        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function remove($data, array $context = [])
    {
        $this->entityManager->remove($data);
        $this->entityManager->flush();
    }
}
