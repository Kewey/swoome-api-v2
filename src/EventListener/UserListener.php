<?php
// src/EventListener/SearchIndexer.php
namespace App\EventListener;

use App\Entity\User;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class UserListener
{
    public function __construct(VerifyEmailHelperInterface $verifyEmailHelper, MailerInterface $mailer)
    {
        $this->verifyEmailHelper = $verifyEmailHelper;
        $this->mailer = $mailer;
    }
    // the listener methods receive an argument which gives you access to
    // both the entity object of the event and the entity manager itself
    public function postPersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        // if this listener only applies to certain entity types,
        // add some code to check the entity type as early as possible
        if (!$entity instanceof User) {
            return;
        }

        //$entityManager = $args->getObjectManager();

        $signatureComponents = $this->verifyEmailHelper->generateSignature(
            'app_verify_email',
            $entity->getId(),
            $entity->getEmail(),
            ['id' => $entity->getId()]
        );

        $email = (new TemplatedEmail())
            ->from(new Address('no_reply@swoome.fr', 'Team Swoome'))
            ->to($entity->getEmail())
            ->subject('Bienvenue chez Swoome ' . $entity->getUsername() . ', confirme ton mail !')
            ->htmlTemplate('emails/confirm.html.twig')
            ->context([
                'url' => $signatureComponents->getSignedUrl(),
            ]);

        $this->mailer->send($email);
    }
}
