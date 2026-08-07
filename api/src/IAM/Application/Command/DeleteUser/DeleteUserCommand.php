<?php

declare(strict_types=1);

namespace App\IAM\Application\Command\DeleteUser;

use App\Shared\Application\Command\CommandInterface;

final class DeleteUserCommand implements CommandInterface
{
    public function __construct(
        public readonly string $userId,
    ) {
    }
}
