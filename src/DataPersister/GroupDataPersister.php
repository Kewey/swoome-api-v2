<?php
// src/DataPersister/GroupDataPersister.php

namespace App\DataPersister;

use App\Entity\User;
use App\Entity\Group;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use Symfony\Component\Security\Core\Security;
use Hashids\Hashids;

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
        return $data instanceof Group;
    }

    /**
     * @param Group $data
     */
    public function persist($data, array $context = []): void
    {
        $currentUser = $this->security->getUser();
        $data->addMember($currentUser);

        $lastGroup = $this->entityManager->getRepository(Group::class)->findOneBy(array(), array('id' => 'DESC'), 1, 0);
        $lastGroupId = $lastGroup->getId();
        $hashid = new Hashids("Groups", 6);
        $data->setCode(strtoupper($hashid->encode($lastGroupId + 1)));

        $userRepository = $this->entityManager->getRepository(User::class);
        foreach ($data->getMembers() as $user) {
            /** @var UserRepository $userRepository */
            $u = $userRepository->findOneByEmail($user->getEmail());
            // if the user exists, don't persist it
            if ($u !== null) {
                $data->removeMember($user);
                $data->addMember($u);
            } else {
                $this->entityManager->persist($user);
            }
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
