<?php

declare(strict_types=1);

namespace App\IAM\Application\Query\ListAdmins;

use App\IAM\Domain\Contract\Repository\UserRepositoryInterface;
use App\IAM\Domain\Model\User;
use App\Shared\Application\Query\AsQueryHandler;

#[AsQueryHandler]
final class ListAdminsQueryHandler
{
    public function __construct(
        private UserRepositoryInterface $repository,
    ) {
    }

    /**
     * @return list<User>
     */
    public function __invoke(ListAdminsQuery $query): array
    {
        return $this->repository->findAdmins();
    }
}
