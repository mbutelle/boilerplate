<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Symfony\Messenger\Middleware;

use App\Shared\Application\Command\LockableCommandInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\Store\DoctrineDbalPostgreSqlStore;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

class LockableCommandMiddleware implements MiddlewareInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $message = $envelope->getMessage();

        if ($message instanceof LockableCommandInterface) {
            $store = new DoctrineDbalPostgreSqlStore($this->entityManager->getConnection());
            $factory = new LockFactory($store);

            $lock = $factory->createLock($message->getLockKey());
            $lock->acquire(true);

            $next = $stack->next()->handle($envelope, $stack);

            $lock->release();

            return $next;
        }

        return $stack->next()->handle($envelope, $stack);
    }
}
