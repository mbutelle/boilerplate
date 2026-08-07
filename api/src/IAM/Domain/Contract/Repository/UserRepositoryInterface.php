<?php

declare(strict_types=1);

namespace App\IAM\Domain\Contract\Repository;

use App\IAM\Domain\Model\User;
use App\Shared\Domain\Contracts\DomainId;

interface UserRepositoryInterface
{
    public function get(DomainId $id): ?User;

    public function findByEmail(string $email): ?User;

    public function findByResetToken(string $token): ?User;

    /**
     * @return list<User>
     */
    public function findAdmins(): array;

    public function save(User $user): void;

    public function remove(User $user): void;
}
