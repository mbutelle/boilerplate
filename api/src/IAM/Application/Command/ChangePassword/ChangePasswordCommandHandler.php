<?php

declare(strict_types=1);

namespace App\IAM\Application\Command\ChangePassword;

use App\IAM\Domain\Contract\Repository\UserRepositoryInterface;
use App\IAM\Domain\Model\User;
use App\Shared\Application\Command\AsCommandHandler;
use App\Shared\Domain\Contracts\DomainId;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

#[AsCommandHandler]
final class ChangePasswordCommandHandler
{
    public function __construct(
        private UserRepositoryInterface $repository,
        private PasswordHasherFactoryInterface $hasherFactory,
    ) {
    }

    public function __invoke(ChangePasswordCommand $command): User
    {
        $user = $this->repository->get(DomainId::fromString($command->userId));

        if (null === $user) {
            throw new NotFoundHttpException('User not found.');
        }

        $user->setPassword(
            $this->hasherFactory->getPasswordHasher(User::class)->hash($command->plainPassword),
        );

        $this->repository->save($user);

        return $user;
    }
}
