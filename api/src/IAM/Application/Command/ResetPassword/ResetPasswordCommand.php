<?php

declare(strict_types=1);

namespace App\IAM\Application\Command\ResetPassword;

use App\Shared\Application\Command\CommandInterface;

final class ResetPasswordCommand implements CommandInterface
{
    public function __construct(
        public readonly string $token,
        public readonly string $plainPassword,
    ) {
    }
}
