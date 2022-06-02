<?php

namespace App\Controller;

use App\Factory\JsonResponseFactory;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
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
    public function __construct(VerifyEmailHelperInterface $verifyEmailHelper, MailerInterface $mailer, JsonResponseFactory $jsonResponseFactory)
    {
        $this->verifyEmailHelper = $verifyEmailHelper;
        $this->mailer = $mailer;
        $this->jsonResponseFactory = $jsonResponseFactory;
    }

    /**
     * @Route("/base", name="app_base")
     */
    public function basicTwig()
    {
        return $this->render('base.html.twig', []);
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
        $this->addFlash('success', 'Compte vérifié! Vous pouvez maintenant vous connecter.');
        return $this->redirectToRoute('app_base');
    }

    /**
     * @Route("/resend_url", methods={"POST"}, name="resend_url")
     */
    public function resendUrl(Request $request, UserRepository $userRepository)
    {
        $parameters = json_decode($request->getContent(), true);
        $id = $parameters['id'];
        //$email = $parameters['email'];
        //$password = $parameters['password'];

        if (!$id) {
            throw new BadRequestHttpException('un "id" est requis');
        }

        $user = $userRepository->find($id);

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
