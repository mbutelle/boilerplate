<?php

declare(strict_types=1);

namespace App\Security\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Security\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;

class MeProvider implements ProviderInterface
{
    public function __construct(
        private Security $security,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?User
    {
        $user = $this->security->getUser();

        if ($user instanceof User) {
            return $user;
        }

        return null;
    }
}
