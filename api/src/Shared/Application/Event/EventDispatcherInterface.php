<?php

declare(strict_types=1);

namespace App\Shared\Application\Event;

interface EventDispatcherInterface
{
    public function dispatch(EventInterface $event): void;
}
