<?php

declare(strict_types=1);

namespace App\IAM\Infrastructure\Event;

use App\IAM\Application\Event\AccountCreated;
use App\IAM\Infrastructure\Message\SendAccountCreatedEmail as SendAccountCreatedEmailMessage;
use App\Shared\Application\Event\AsEventHandler;
use App\Shared\Application\Event\EventHandlerInterface;
use App\Shared\Infrastructure\Message\MessageBusInterface;

/**
 * L'abonné vit en infrastructure : c'est ce qui lui donne le droit de manipuler
 * un bus concret. Il ne fait pas le travail lui-même, il publie un message
 * asynchrone ne portant que l'identifiant — l'envoi du mail sort ainsi du cycle
 * de la requête HTTP, et une panne du serveur SMTP ne fait plus échouer la
 * création de compte.
 */
#[AsEventHandler]
final class SendAccountCreatedEmail implements EventHandlerInterface
{
    public function __construct(
        private MessageBusInterface $messageBus,
    ) {
    }

    public function __invoke(AccountCreated $event): void
    {
        $this->messageBus->dispatch(new SendAccountCreatedEmailMessage((string) $event->user->getId()));
    }
}
