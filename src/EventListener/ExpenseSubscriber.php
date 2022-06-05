<?php

namespace App\EventListener;

use App\Entity\Balance;
use App\Entity\Expense;
use App\Entity\Group;
use App\Entity\Refund;
use App\Repository\BalanceRepository;
use App\Repository\ExpenseRepository;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Security;

class ExpenseSubscriber implements EventSubscriberInterface
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

    // this method can only return the event names; you cannot define a
    // custom method name to execute when each event triggers
    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::postPersist,
            Events::postUpdate,
            Events::postRemove
        ];
    }

    // callback methods must be called exactly like the events they listen to;
    // they receive an argument of type LifecycleEventArgs, which gives you access
    // to both the entity object of the event and the entity manager itself
    public function prePersist(LifecycleEventArgs $args): void
    {
        $this->addDatasToExpense('prePersist', $args);
        $this->entityManager->flush();
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $this->removeRefunds('persist', $args);
        $this->calculateBalances('persist', $args);
    }

    public function postUpdate(LifecycleEventArgs $args): void
    {
        $this->removeRefunds('update', $args);
        $this->calculateAllBalances('update', $args);
    }

    public function postRemove(LifecycleEventArgs $args): void
    {
        $this->removeRefunds('delete', $args);
        $this->calculateAllBalances('delete', $args);
    }

    private function addDatasToExpense(string $action, LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Expense) {
            return;
        }
        $currentUser = $this->security->getUser();

        if (!$entity->getMadeBy()) {
            $entity->setMadeBy($currentUser);
        }

        if (!$entity->getExpenseAt()) {
            $entity->setExpenseAt(new DateTime());
        }

        $this->entityManager->flush();
    }

    public function removeRefunds(string $action, LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if (!$entity instanceof Expense) {
            return;
        }

        $oldRefunds = $entity->getExpenseGroup()->getRefunds();
        foreach ($oldRefunds as $oldRefund) {
            $this->entityManager->remove($oldRefund);
        }
    }

    public function calculateAllBalances(string $action, LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if (!$entity instanceof Expense) {
            return;
        }

        $balances = [];
        foreach ($entity->getExpenseGroup()->getMembers() as $user) {
            $balanceValue = 0;
            foreach ($this->expenseRepository->findExpenseByUserAndGroup($user, $entity->getExpenseGroup()) as $expense) {
                if ($user == $expense->getMadeBy()) {
                    if ($expense->getParticipants()->count() == 1 && !$expense->getParticipants()->contains($user)) {
                        $balanceValue += $expense->getPrice();
                    } else {
                        $balanceValue += $expense->getPrice() - ($expense->getPrice() / $expense->getParticipants()->count());
                    }
                } elseif ($expense->getParticipants()->contains($user)) {
                    $balanceValue += - ($expense->getPrice() / $expense->getParticipants()->count());
                } else {
                    $balanceValue += 0;
                }
            }

            $lastBalance = $this->balanceRepository->findBalanceByUserByGroup($user, $entity->getExpenseGroup());
            $lastBalance->setValue($balanceValue);

            /*TODO Recruter mathématicien*/
            if (-3 < $lastBalance->getValue() && $lastBalance->getValue() < 3) {
                $lastBalance->setValue(0);
            }

            $this->entityManager->persist($lastBalance);
            $balances[] = clone $lastBalance;
        }
        $this->calculateRefunds($balances);
        $this->entityManager->flush();
    }

    public function calculateBalances(string $action, LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if (!$entity instanceof Expense) {
            return;
        }

        $balances = [];
        foreach ($entity->getExpenseGroup()->getMembers() as $user) {
            if ($user == $entity->getMadeBy()) {
                if ($entity->getParticipants()->count() == 1 && !$entity->getParticipants()->contains($user)) {
                    $balanceValue = $entity->getPrice();
                } else {
                    $balanceValue = $entity->getPrice() - ($entity->getPrice() / $entity->getParticipants()->count());
                }
            } elseif ($entity->getParticipants()->contains($user)) {
                $balanceValue = - ($entity->getPrice() / $entity->getParticipants()->count());
            } else {
                $balanceValue = 0;
            }

            $balance = $this->balanceRepository->findBalanceByUserByGroup($user, $entity->getExpenseGroup());
            if (!$balance) {
                $balance = new Balance;
                $balance->setBalanceUser($user);
                $balance->setBalanceGroup($entity->getExpenseGroup());
                $balance->setValue($balanceValue);
            } else {
                $balance->setValue($balance->getValue() + $balanceValue);
            }

            /*TODO Recruter mathématicien*/
            if (-3 < $balance->getValue() && $balance->getValue() < 3) {
                $balance->setValue(0);
            }

            $this->entityManager->persist($balance);
            $balances[] = clone $balance;
        }
        $this->calculateRefunds($balances);
        $this->entityManager->flush();
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
}
