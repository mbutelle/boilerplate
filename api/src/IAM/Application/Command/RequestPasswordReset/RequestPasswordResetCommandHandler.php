<?php

declare(strict_types=1);

namespace App\IAM\Application\Command\RequestPasswordReset;

use App\IAM\Application\Event\PasswordResetRequested;
use App\IAM\Domain\Contract\Repository\UserRepositoryInterface;
use App\Shared\Application\Command\AsCommandHandler;
use App\Shared\Application\Event\EventDispatcherInterface;

#[AsCommandHandler]
final class RequestPasswordResetCommandHandler
{
    public function __construct(
        private UserRepositoryInterface $repository,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function __invoke(RequestPasswordResetCommand $command): void
    {
        $user = $this->repository->findByEmail($command->email);

        // Silence volontaire sur une adresse inconnue : répondre différemment
        // transformerait l'endpoint en oracle d'existence de comptes.
        if (null === $user) {
            return;
        }

        $user->requestReset();
        $this->repository->save($user);

        $this->eventDispatcher->dispatch(new PasswordResetRequested($user));
    }
}
