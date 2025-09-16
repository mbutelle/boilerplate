<?php

declare(strict_types=1);

namespace App\Shared\Application\Command;

interface LockableCommandInterface extends CommandInterface
{
    public function getLockKey(): string;
}
