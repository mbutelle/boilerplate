<?php

declare(strict_types=1);

namespace App\IAM\Application\Command\DeleteUser;

use App\IAM\Domain\Contract\Repository\UserRepositoryInterface;
use App\Shared\Application\Command\AsCommandHandler;
use App\Shared\Domain\Contracts\DomainId;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[AsCommandHandler]
final class DeleteUserCommandHandler
{
    public function __construct(
        private UserRepositoryInterface $repository,
    ) {
    }

    public function __invoke(DeleteUserCommand $command): void
    {
        $user = $this->repository->get(DomainId::fromString($command->userId));

        if (null === $user) {
            throw new NotFoundHttpException('User not found.');
        }

        $this->repository->remove($user);
    }
}
