<?php
// src/DataPersister/GroupDataPersister.php

namespace App\DataPersister;

use App\Entity\User;
use App\Entity\Group;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Balance;
use Symfony\Component\Security\Core\Security;
use Hashids\Hashids;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 *
 */
class GroupDataPersister implements ContextAwareDataPersisterInterface
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

    /**
     * {@inheritdoc}
     */
    public function supports($data, array $context = []): bool
    {
        return $data instanceof Group;
    }

    /**
     * @param Group $data
     */
    public function persist($data, array $context = []): void
    {
        $currentUser = $this->security->getUser();

        if (
            ($context['collection_operation_name'] ?? null) === 'post' ||
            ($context['graphql_operation_name'] ?? null) === 'create'
        ) {
            $lastGroupId = 0;
            $lastGroup = $this->entityManager->getRepository(Group::class)->findOneBy(array(), array('id' => 'DESC'), 1, 0);
            if ($lastGroup) {
                $lastGroupId = $lastGroup->getId();
            }
            $data->addMember($currentUser);
            $hashid = new Hashids("Groups", 6);
            $data->setCode(strtoupper($hashid->encode($lastGroupId + 1)));
        }

        $userRepository = $this->entityManager->getRepository(User::class);
        foreach ($data->getMembers() as $user) {
            /** @var UserRepository $userRepository */
            $u = $userRepository->findOneByEmail($user->getEmail());
            // if the user exists, don't persist it
            if ($u !== null) {
                $data->removeMember($user);
                $data->addMember($u);
                if (
                    ($context['collection_operation_name'] ?? null) === 'post' ||
                    ($context['graphql_operation_name'] ?? null) === 'create'
                ) {
                    $data->addBalance($this->createEmptyBalance($u));
                }
            } else {
                $this->entityManager->persist($user);
            }
        }

        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }

    public function createEmptyBalance($user)
    {
        $balance = new Balance;
        $balance->setValue(0);
        $balance->setBalanceUser($user);
        return $balance;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($data, array $context = [])
    {
        $currentUser = $this->security->getUser();
        if (!in_array($currentUser, $data->getMembers()))
            throw new AccessDeniedHttpException('Vous n\'appartenez pas à ce groupe');
        $this->entityManager->remove($data);
        $this->entityManager->flush();
    }
}
