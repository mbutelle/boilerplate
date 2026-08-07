<?php

declare(strict_types=1);

namespace App\IAM\Application\Command\ResetPassword;

use App\IAM\Domain\Contract\Repository\UserRepositoryInterface;
use App\IAM\Domain\Model\User;
use App\Shared\Application\Command\AsCommandHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

#[AsCommandHandler]
final class ResetPasswordCommandHandler
{
    public function __construct(
        private UserRepositoryInterface $repository,
        private PasswordHasherFactoryInterface $hasherFactory,
    ) {
    }

    public function __invoke(ResetPasswordCommand $command): void
    {
        $user = $this->repository->findByResetToken($command->token);

        if (null === $user) {
            throw new NotFoundHttpException('This reset link is no longer valid.');
        }

        $user->resetPassword(
            $this->hasherFactory->getPasswordHasher(User::class)->hash($command->plainPassword),
        );

        $this->repository->save($user);
    }
}
