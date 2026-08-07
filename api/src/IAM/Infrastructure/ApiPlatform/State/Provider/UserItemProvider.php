<?php

declare(strict_types=1);

namespace App\IAM\Infrastructure\ApiPlatform\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\IAM\Domain\Contract\Repository\UserRepositoryInterface;
use App\IAM\Infrastructure\ApiPlatform\Resource\UserResource;
use App\Shared\Domain\Contracts\DomainId;
use Symfony\Component\Uid\Uuid;

/**
 * @implements ProviderInterface<UserResource>
 */
final class UserItemProvider implements ProviderInterface
{
    public function __construct(
        private UserRepositoryInterface $repository,
    ) {
    }

    /**
     * @param array<string, mixed> $uriVariables
     * @param array<string, mixed> $context
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?UserResource
    {
        $id = $uriVariables['id'] ?? null;

        if (!is_string($id) || !Uuid::isValid($id)) {
            return null;
        }

        $user = $this->repository->get(DomainId::fromString($id));

        return null === $user ? null : UserResource::fromDomain($user);
    }
}
