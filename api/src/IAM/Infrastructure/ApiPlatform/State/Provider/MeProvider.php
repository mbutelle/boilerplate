<?php

declare(strict_types=1);

namespace App\IAM\Infrastructure\ApiPlatform\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\IAM\Domain\Model\User;
use App\IAM\Infrastructure\ApiPlatform\Resource\UserResource;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * @implements ProviderInterface<UserResource>
 */
final class MeProvider implements ProviderInterface
{
    public function __construct(
        private Security $security,
    ) {
    }

    /**
     * @param array<string, mixed> $uriVariables
     * @param array<string, mixed> $context
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?UserResource
    {
        $user = $this->security->getUser();

        return $user instanceof User ? UserResource::fromDomain($user) : null;
    }
}
