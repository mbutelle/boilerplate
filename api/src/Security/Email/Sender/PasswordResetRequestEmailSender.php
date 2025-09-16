<?php

declare(strict_types=1);

namespace App\Security\Email\Sender;

use App\Security\Entity\User;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class PasswordResetRequestEmailSender
{
    public function __construct(
        private TranslatorInterface $translator,
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
        $resetUrl = sprintf('%s/auth/reset-password/%s', $this->adminUrl, $user->resetToken);

        if (!$user->isAdmin()) {
            $resetUrl = sprintf($this->passwordResetUrl, $user->resetToken);
        }

        $locale = $user->account?->getLocale() ?? 'fr_FR';

        $email = (new Email())
            ->subject($this->translator->trans('email.password_reset.subject', [], null, $locale))
            ->from(new Address(
                $this->senderEmail,
                $this->senderEmail
            ))
            ->to(new Address(
                $user->email,
                $user->email
            ))
            ->html($this->twig->render('email/security/password_reset.html.twig', [
                'user' => $user,
                'resetUrl' => $resetUrl,
                'locale' => $locale,
            ]))
        ;

        $this->mailer->send($email);
    }
}
