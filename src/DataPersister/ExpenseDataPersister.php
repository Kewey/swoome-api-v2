<?php
// src/DataPersister/ExpenseDataPersister.php

namespace App\DataPersister;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Expense;
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
        Security $security
    ) {
        $this->entityManager = $entityManager;
        $this->security = $security;
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

        foreach ($data->getParticipants() as $user) {
            $this->entityManager->persist($user);
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
