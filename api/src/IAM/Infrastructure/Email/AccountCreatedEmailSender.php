<?php

declare(strict_types=1);

namespace App\IAM\Infrastructure\Email;

use App\IAM\Domain\Model\User;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class AccountCreatedEmailSender
{
    public function __construct(
        private MailerInterface $mailer,
        private Environment $twig,
        #[Autowire('%env(SENDER_EMAIL)%')]
        private string $senderEmail,
        #[Autowire('%env(ADMIN_URL)%')]
        private string $adminUrl,
        #[Autowire('%env(PASSWORD_RESET_URL)%')]
        private string $passwordResetUrl,
    ) {
    }

    public function send(User $user): void
    {
        $email = (new Email())
            ->subject('Création de votre compte – Veuillez définir votre mot de passe')
            ->from(new Address($this->senderEmail, $this->senderEmail))
            ->to(new Address($user->getEmail(), $user->getEmail()))
            ->html($this->twig->render('email/security/admin_request.html.twig', [
                'user' => $user,
                'resetUrl' => $this->buildResetUrl($user),
            ]))
        ;

        $this->mailer->send($email);
    }

    private function buildResetUrl(User $user): string
    {
        $token = (string) $user->getResetToken();

        return $user->isAdmin()
            ? sprintf('%s/auth/reset-password/%s', $this->adminUrl, $token)
            : sprintf($this->passwordResetUrl, $token);
    }
}
