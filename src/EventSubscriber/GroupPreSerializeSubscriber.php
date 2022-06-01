<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\User;
use App\Entity\Group;
use App\Repository\UserRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class GroupPreSerializeSubscriber implements EventSubscriberInterface
{
    private $tokenStorage;
    private $userRepository;
    private $authorizationChecker;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        UserRepository $userRepository,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->userRepository = $userRepository;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['groupPreSerialize', EventPriorities::PRE_SERIALIZE],
        ];
    }

    public function groupPreSerialize(ViewEvent $event)
    {
        $group = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (!$group instanceof Group || (Request::METHOD_GET != $method)) {
            return;
        }

        $currentUser = $this->tokenStorage->getToken()->getUser();
        if (!$currentUser instanceof User)
            return;

        if (!in_array($currentUser, $group->getMembers()))
            throw new AccessDeniedHttpException('Vous n\'appartenez pas à ce groupe');
    }
}
