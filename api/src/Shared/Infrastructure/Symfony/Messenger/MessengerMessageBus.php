<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Symfony\Messenger;

use App\Shared\Infrastructure\Message\MessageInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;

final class MessengerMessageBus
{
    private MessageBusInterface $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function dispatch(MessageInterface $message): void
    {
        try {
            $this->messageBus->dispatch($message);
        } catch (HandlerFailedException $e) {
            if ($exception = current($e->getWrappedExceptions())) {
                throw $exception;
            }

            throw $e;
        }
    }
}
