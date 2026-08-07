<?php

declare(strict_types=1);

namespace App\IAM\Application\Event;

use App\IAM\Domain\Model\User;
use App\Shared\Application\Event\EventInterface;

/**
 * Un compte vient d'être créé sans mot de passe et attend que son propriétaire
 * en définisse un.
 */
final class AccountCreated implements EventInterface
{
    public function __construct(
        public readonly User $user,
    ) {
    }
}
