<?php

declare(strict_types=1);

namespace App\IAM\Application\Event;

use App\IAM\Domain\Model\User;
use App\Shared\Application\Event\EventInterface;

/**
 * Un utilisateur a demandé la réinitialisation de son mot de passe et un jeton
 * vient d'être émis pour lui.
 */
final class PasswordResetRequested implements EventInterface
{
    public function __construct(
        public readonly User $user,
    ) {
    }
}
