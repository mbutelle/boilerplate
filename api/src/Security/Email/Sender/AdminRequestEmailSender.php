<?php

declare(strict_types=1);

namespace App\Security\Email\Sender;

use App\Security\Entity\User;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class AdminRequestEmailSender
{
    public function __construct(
        private MailerInterface $mailer,
        private Environment $twig,
        #[Autowire('%env(ADMIN_URL)%')]
        private string $adminUrl,
        #[Autowire('%env(PASSWORD_RESET_URL)%')]
        private string $passwordResetUrl,
    ) {
    }

    public function send(User $user): void
    {
        $resetUrl = sprintf('%s/auth/reset-password/%s', $this->adminUrl, $user->resetToken);

        if (!$user->isAdmin()) {
            $resetUrl = sprintf($this->passwordResetUrl, $user->resetToken);
        }

        $email = (new Email())
            ->subject(sprintf(
                'Création de votre compte – Veuillez définir votre mot de passe',
            ))
            ->to(new Address(
                $user->email,
                $user->email
            ))
            ->html($this->twig->render('email/security/admin_request.html.twig', [
                'user' => $user,
                'resetUrl' => $resetUrl,
            ]))
        ;

        $this->mailer->send($email);
    }
}
