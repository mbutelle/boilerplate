<?php

declare(strict_types=1);

namespace App\IAM\Application\Command\ChangePassword;

use App\Shared\Application\Command\CommandInterface;

final class ChangePasswordCommand implements CommandInterface
{
    public function __construct(
        public readonly string $userId,
        public readonly string $plainPassword,
    ) {
    }
}
