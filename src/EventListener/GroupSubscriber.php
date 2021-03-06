<?php

namespace App\EventListener;

use App\Entity\Balance;
use App\Entity\Group;
use App\Repository\BalanceRepository;
use App\Repository\ExpenseTypeRepository;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class GroupSubscriber implements EventSubscriberInterface
{

    public function __construct(EntityManagerInterface $entityManager, BalanceRepository $balanceRepository, ExpenseTypeRepository $expenseTypeRepository)
    {
        $this->entityManager = $entityManager;
        $this->balanceRepository = $balanceRepository;
        $this->expenseTypeRepository = $expenseTypeRepository;
    }

    // this method can only return the event names; you cannot define a
    // custom method name to execute when each event triggers
    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
            Events::postUpdate,
        ];
    }

    // callback methods must be called exactly like the events they listen to;
    // they receive an argument of type LifecycleEventArgs, which gives you access
    // to both the entity object of the event and the entity manager itself
    public function postPersist(LifecycleEventArgs $args): void
    {
        $this->addBalancesToGroup('persist', $args);
        $this->addExpenseTypesToGroup('persist', $args);
    }

    public function postUpdate(LifecycleEventArgs $args): void
    {
        $this->addBalancesToGroup('update', $args);
    }

    private function addExpenseTypesToGroup(string $action, LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Group) {
            return;
        }

        $espenseTypes = $this->expenseTypeRepository->findExpenseTypeByGroupType($entity->getType());
        foreach ($espenseTypes as $expenseType) {
            $entity->addExpenseType($expenseType);
        }
        $this->entityManager->flush();
    }

    private function addBalancesToGroup(string $action, LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Group) {
            return;
        }

        foreach ($entity->getMembers() as $user) {
            if (!$this->balanceRepository->findBalanceByUserByGroup($user, $entity)) {
                $entity->addBalance($this->createEmptyBalance($user));
            }
            $this->entityManager->persist($user);
        }
        $this->entityManager->flush();
    }

    public function createEmptyBalance($user)
    {
        $balance = new Balance;
        $balance->setBalanceUser($user);
        return $balance;
    }
}
