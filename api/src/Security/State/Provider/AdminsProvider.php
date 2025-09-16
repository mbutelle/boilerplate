<?php

declare(strict_types=1);

namespace App\Security\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Security\Entity\User;
use App\Security\Repository\UserRepository;

/**
 * Minimal provider that returns all users with ROLE_ADMIN.
 */
class AdminsProvider implements ProviderInterface
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        // Minimal and portable approach: fetch all and filter in PHP.
        // Can be optimized later with DB-specific JSON containment queries.
        $all = $this->userRepository->findAll();

        return array_values(array_filter($all, static function ($u): bool {
            if (!$u instanceof User) {
                return false;
            }

            // Include both Admin and Super Admin
            return in_array(User::ROLE_ADMIN, $u->roles, true) || in_array(User::ROLE_SUPER_ADMIN, $u->roles, true);
        }));
    }
}
