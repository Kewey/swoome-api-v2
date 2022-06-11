<?php

namespace App\EventListener;

use App\Entity\Expense;
use App\Entity\Refund;
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
    ) {
        $this->entityManager = $entityManager;
        $this->security = $security;
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
        $entity = $args->getObject();

        if (!$entity instanceof Expense) {
            return;
        }

        $this->addDatasToExpense($entity);
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Expense) {
            return;
        }

        $this->handleRefunds($entity);
    }

    public function postUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Expense) {
            return;
        }

        $this->handleRefunds($entity);
    }

    public function postRemove(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Expense) {
            return;
        }

        $this->handleRefunds($entity);
    }

    private function addDatasToExpense($expense): void
    {

        $currentUser = $this->security->getUser();

        if (!$expense->getMadeBy()) {
            $expense->setMadeBy($currentUser);
        }

        if (!$expense->getExpenseAt()) {
            $expense->setExpenseAt(new DateTime());
        }

        $this->entityManager->flush();
    }

    public function handleRefunds($expense)
    {
        $this->removeRefunds($expense);
        $this->calculateRefunds($expense);
        $this->entityManager->flush();
    }

    public function removeRefunds($expense)
    {
        $oldRefunds = $expense->getExpenseGroup()->getRefunds();
        foreach ($oldRefunds as $oldRefund) {
            $this->entityManager->remove($oldRefund);
        }
    }

    public function calculateRefunds($expense)
    {

        $balances = clone $expense->getExpenseGroup()->getBalances();
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
                    if ($positiveBalance->getRefundTemporaryValue() > 0 && $negativeBalance->getRefundTemporaryValue() < 0) {
                        if ($positiveBalance->getRefundTemporaryValue() >= - ($negativeBalance->getRefundTemporaryValue())) {
                            $newPositiveBalance = $positiveBalance->getRefundTemporaryValue() + $negativeBalance->getRefundTemporaryValue();
                            $newNegativeBalance = $negativeBalance->getRefundTemporaryValue() + (-$negativeBalance->getRefundTemporaryValue());
                        } else {
                            $newPositiveBalance = 0;
                            $newNegativeBalance = $negativeBalance->getRefundTemporaryValue() + $positiveBalance->getRefundTemporaryValue();
                        }
                        $refund = new Refund();
                        $refund->setRefundGroup($positiveBalance->getBalanceGroup());
                        $refund->setPrice($newNegativeBalance - $negativeBalance->getRefundTemporaryValue());
                        $refund->setRefunder($negativeBalance->getBalanceUser());
                        $refund->setReceiver($positiveBalance->getBalanceUser());

                        $this->entityManager->persist($refund);

                        $positiveBalance->setRefundTemporaryValue($newPositiveBalance);
                        $negativeBalance->setRefundTemporaryValue($newNegativeBalance);
                    }
                }
            }
        }
    }
}
