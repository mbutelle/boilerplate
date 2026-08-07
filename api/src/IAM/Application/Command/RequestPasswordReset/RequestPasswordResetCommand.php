<?php

declare(strict_types=1);

namespace App\IAM\Application\Command\RequestPasswordReset;

use App\Shared\Application\Command\CommandInterface;

final class RequestPasswordResetCommand implements CommandInterface
{
    public function __construct(
        public readonly string $email,
    ) {
    }
}
