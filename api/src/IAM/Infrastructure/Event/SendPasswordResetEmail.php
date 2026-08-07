<?php

declare(strict_types=1);

namespace App\IAM\Infrastructure\Event;

use App\IAM\Application\Event\PasswordResetRequested;
use App\IAM\Infrastructure\Email\PasswordResetEmailSender;
use App\Shared\Application\Event\AsEventHandler;
use App\Shared\Application\Event\EventHandlerInterface;

#[AsEventHandler]
final class SendPasswordResetEmail implements EventHandlerInterface
{
    public function __construct(
        private PasswordResetEmailSender $sender,
    ) {
    }

    public function __invoke(PasswordResetRequested $event): void
    {
        $this->sender->send($event->user);
    }
}
