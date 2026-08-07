<?php

declare(strict_types=1);

namespace App\IAM\Application\Command\CreateUser;

use App\Shared\Application\Command\CommandInterface;

final class CreateUserCommand implements CommandInterface
{
    /**
     * @param string|null $plainPassword null pour laisser l'utilisateur définir
     *                                   son mot de passe via le lien envoyé par mail
     */
    public function __construct(
        public readonly string $email,
        public readonly ?string $plainPassword = null,
        public readonly bool $admin = false,
    ) {
    }
}
