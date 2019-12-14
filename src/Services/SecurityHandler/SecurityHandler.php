<?php

namespace App\Services\SecurityHandler;

use App\Entity\PasswordReset;
use App\Entity\User;
use App\Services\Mailer\Envelope;
use App\Services\Mailer\MailerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

final class SecurityHandler implements SecurityHandlerInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var MailerInterface
     */
    private $mailer;
    /**
     * @var Environment
     */
    private $twig;
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct(EntityManagerInterface $entityManager,
                                MailerInterface $mailer,
                                Environment $twig,
                                UrlGeneratorInterface $urlGenerator)
    {
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function doAllTheNecessaryForThisUserWhoHaveLostHisPassword(User $user): void
    {
        // Create an instance of PasswordReset
        $passwordReset = new PasswordReset($user);
        $this->entityManager->persist($passwordReset);
        $this->entityManager->flush($passwordReset);

        // Send an email with the recovery link
        $envelope = new Envelope(
            $user->getUsername(), $user->getEmail(),
            'Récupération de votre mot de passe',
            $this->twig->render('email/reset-password.html.twig', [
                'resetPasswordLink' => $this->urlGenerator->generate('app.resetPassword', ['token' => $passwordReset->getToken()], UrlGeneratorInterface::ABSOLUTE_URL),
            ])
        );

        $this->mailer->send($envelope);
    }
}
