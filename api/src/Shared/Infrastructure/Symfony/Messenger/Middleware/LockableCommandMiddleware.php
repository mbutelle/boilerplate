<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Symfony\Messenger\Middleware;

use App\Shared\Application\Command\LockableCommandInterface;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

/**
 * Sérialise l'exécution des commandes qui déclarent une clé de verrou : deux
 * commandes portant la même clé ne s'exécutent jamais en parallèle.
 *
 * La fabrique est injectée, et non construite ici : le magasin vient de
 * config/packages/lock.yaml, donc de LOCK_DSN. La version précédente
 * instanciait un DoctrineDbalPostgreSqlStore en dur alors que la base est
 * MySQL — et symfony/lock n'était même pas installé, si bien que dispatcher une
 * commande verrouillable produisait une erreur fatale.
 */
class LockableCommandMiddleware implements MiddlewareInterface
{
    public function __construct(
        private LockFactory $lockFactory,
    ) {
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $message = $envelope->getMessage();

        if (!$message instanceof LockableCommandInterface) {
            return $stack->next()->handle($envelope, $stack);
        }

        $lock = $this->lockFactory->createLock($message->getLockKey());
        $lock->acquire(true);

        try {
            return $stack->next()->handle($envelope, $stack);
        } finally {
            // Dans un finally : si le handler lève, le verrou doit quand même
            // être relâché, sinon la clé reste bloquée jusqu'à expiration et
            // toutes les commandes suivantes attendent pour rien.
            $lock->release();
        }
    }
}
