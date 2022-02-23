<?php
// src/DataPersister/GroupDataPersister.php

namespace App\DataPersister;

use App\Entity\User;
use App\Entity\Group;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\String\Slugger\SluggerInterface;
use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;

/**
 *
 */
class GroupDataPersister implements ContextAwareDataPersisterInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $_entityManager;

    /**
     * @param SluggerInterface
     */
    private $_slugger;

    /**
     * @param Request
     */
    private $_request;

    public function __construct(
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger,
        RequestStack $request
    ) {
        $this->_entityManager = $entityManager;
        $this->_slugger = $slugger;
        $this->_request = $request->getCurrentRequest();
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
    public function persist($data, array $context = [])
    {
        $userRepository = $this->_entityManager->getRepository(User::class);
        foreach ($data->getMembers() as $user) {
            $u = $userRepository->findOneByEmail($user->getEmail());

            // if the user exists, don't persist it
            if ($u !== null) {
                $data->removeMember($user);
                $data->addMember($u);
            } else {
                $this->_entityManager->persist($user);
            }
        }

        $this->_entityManager->persist($data);
        $this->_entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function remove($data, array $context = [])
    {
        $this->_entityManager->remove($data);
        $this->_entityManager->flush();
    }
}
