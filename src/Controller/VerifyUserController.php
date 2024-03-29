<?php

namespace App\Controller;

use App\Factory\JsonResponseFactory;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use Symfony\Component\Routing\Annotation\Route;

class VerifyUserController extends AbstractController
{
    public function __construct(VerifyEmailHelperInterface $verifyEmailHelper, MailerInterface $mailer, JsonResponseFactory $jsonResponseFactory, AdminUrlGenerator $adminUrlGenerator)
    {
        $this->verifyEmailHelper = $verifyEmailHelper;
        $this->mailer = $mailer;
        $this->jsonResponseFactory = $jsonResponseFactory;
        $this->adminUrlGenerator = $adminUrlGenerator;
    }

    /**
     * @Route("/verify", name="app_verify_email")
     */
    public function verifyUserEmail(Request $request, VerifyEmailHelperInterface $verifyEmailHelper, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $userRepository->find($request->query->get('id'));
        if (!$user) {
            throw $this->createNotFoundException();
        }
        try {
            $verifyEmailHelper->validateEmailConfirmation(
                $request->getUri(),
                $user->getId(),
                $user->getEmail(),
            );
        } catch (VerifyEmailExceptionInterface $e) {
            $this->addFlash('error', $e->getReason());
            return $this->redirectToRoute('app_base');
        }
        $user->setIsVerified(true);
        $entityManager->flush();
        $this->addFlash('success', 'Compte vérifié! Vous pouvez maintenant vous connecter. Vous allez être redirigé vers l\'application Swoome.');
        return $this->render('account_confirm/index.html.twig', []);
    }

    /**
     * @Route("/api/auth/resend_mail", methods={"POST"}, name="resend_mail")
     */
    public function resendMail(Request $request, UserRepository $userRepository)
    {
        $parameters = json_decode($request->getContent(), true);
        $email = $parameters['email'];
        //$email = $parameters['email'];
        //$password = $parameters['password'];

        if (!$email) {
            throw new BadRequestHttpException('un "email" est requis');
        }

        $user = $userRepository->findOneByEmail($email);

        if (!$user) {
            throw $this->createNotFoundException();
        }

        $signatureComponents = $this->verifyEmailHelper->generateSignature(
            'app_verify_email',
            $user->getId(),
            $user->getEmail(),
            ['id' => $user->getId()]
        );

        $email = (new TemplatedEmail())
            ->from(new Address('no_reply@swoome.fr', 'Team Swoome'))
            ->to($user->getEmail())
            ->subject('Bienvenue chez Swoome ' . $user->getUsername() . ', confirme ton mail !')
            ->htmlTemplate('emails/confirm.html.twig')
            ->context([
                'url' => $signatureComponents->getSignedUrl(),
            ]);

        $this->mailer->send($email);

        return $this->jsonResponseFactory->create($user);
    }
}
