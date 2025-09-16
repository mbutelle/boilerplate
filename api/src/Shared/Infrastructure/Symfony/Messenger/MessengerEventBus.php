<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Symfony\Messenger;

use App\Shared\Application\Event\EventDispatcherInterface;
use App\Shared\Application\Event\EventInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;

final class MessengerEventBus implements EventDispatcherInterface
{
    private MessageBusInterface $eventBus;

    public function __construct(MessageBusInterface $eventBus)
    {
        $this->eventBus = $eventBus;
    }

    public function dispatch(EventInterface $event): void
    {
        try {
            $this->eventBus->dispatch(
                (new Envelope($event))
                    ->with(new DispatchAfterCurrentBusStamp())
            );
        } catch (HandlerFailedException $e) {
            if ($exception = current($e->getWrappedExceptions())) {
                throw $exception;
            }

            throw $e;
        }
    }
}
