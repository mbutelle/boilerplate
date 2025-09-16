<?php

declare(strict_types=1);

namespace App\Security\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Security\ApiResource\PasswordResetRequest;
use App\Security\Repository\UserRepository;

class UserByResetTokenProvider implements ProviderInterface
{
    public function __construct(
        private UserRepository $userRepository,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if (isset($uriVariables['token'])) {
            $user = $this->userRepository->findOneBy(['resetToken' => $uriVariables['token']]);

            if (!$user) {
                return null;
            }

            return new PasswordResetRequest(
                $user->resetToken,
                $user->email,
                $user->password,
            );
        }

        return null;
    }
}
