<?php

declare(strict_types=1);

namespace App\IAM\Infrastructure\Event;

use App\IAM\Application\Event\AccountCreated;
use App\IAM\Infrastructure\Email\AccountCreatedEmailSender;
use App\Shared\Application\Event\AsEventHandler;
use App\Shared\Application\Event\EventHandlerInterface;

/**
 * L'abonné vit en infrastructure : c'est ce qui lui donne le droit de manipuler
 * un expéditeur concret. Un abonné en Application ne connaîtrait qu'un port.
 */
#[AsEventHandler]
final class SendAccountCreatedEmail implements EventHandlerInterface
{
    public function __construct(
        private AccountCreatedEmailSender $sender,
    ) {
    }

    public function __invoke(AccountCreated $event): void
    {
        $this->sender->send($event->user);
    }
}
