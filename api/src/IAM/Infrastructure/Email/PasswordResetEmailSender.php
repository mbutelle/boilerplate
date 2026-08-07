<?php

declare(strict_types=1);

namespace App\IAM\Infrastructure\Email;

use App\IAM\Domain\Model\User;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class PasswordResetEmailSender
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
        #[Autowire('%kernel.default_locale%')]
        private string $defaultLocale,
    ) {
    }

    public function send(User $user): void
    {
        $email = (new Email())
            ->subject($this->translator->trans('email.password_reset.subject', [], null, $this->defaultLocale))
            ->from(new Address($this->senderEmail, $this->senderEmail))
            ->to(new Address($user->getEmail(), $user->getEmail()))
            ->html($this->twig->render('email/security/password_reset.html.twig', [
                'user' => $user,
                'resetUrl' => $this->buildResetUrl($user),
                'locale' => $this->defaultLocale,
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
