<?php

declare(strict_types=1);

namespace App\IAM\Application\Command\CreateUser;

use App\IAM\Application\Event\AccountCreated;
use App\IAM\Domain\Contract\Repository\UserRepositoryInterface;
use App\IAM\Domain\Model\User;
use App\Shared\Application\Command\AsCommandHandler;
use App\Shared\Application\Event\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

#[AsCommandHandler]
final class CreateUserCommandHandler
{
    public function __construct(
        private UserRepositoryInterface $repository,
        private PasswordHasherFactoryInterface $hasherFactory,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function __invoke(CreateUserCommand $command): User
    {
        if (null !== $this->repository->findByEmail($command->email)) {
            throw new ConflictHttpException('A user already exists with this email.');
        }

        $user = $command->admin
            ? User::createAdmin($command->email)
            : User::create($command->email);

        if (null === $command->plainPassword) {
            // Aucun mot de passe fourni : le compte naît inutilisable et c'est le
            // lien de réinitialisation envoyé par mail qui l'ouvre.
            $user->requestReset();
        } else {
            $user->setPassword(
                $this->hasherFactory->getPasswordHasher(User::class)->hash($command->plainPassword),
            );
        }

        $this->repository->save($user);

        if (null === $command->plainPassword) {
            $this->eventDispatcher->dispatch(new AccountCreated($user));
        }

        return $user;
    }
}
