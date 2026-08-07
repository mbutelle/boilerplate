<?php

declare(strict_types=1);

namespace App\IAM\Infrastructure\ApiPlatform\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\IAM\Application\Query\ListAdmins\ListAdminsQuery;
use App\IAM\Domain\Model\User;
use App\IAM\Infrastructure\ApiPlatform\Resource\UserResource;
use App\Shared\Application\Query\QueryBusInterface;

/**
 * @implements ProviderInterface<UserResource>
 */
final class AdminCollectionProvider implements ProviderInterface
{
    public function __construct(
        private QueryBusInterface $queryBus,
    ) {
    }

    /**
     * @param array<string, mixed> $uriVariables
     * @param array<string, mixed> $context
     *
     * @return list<UserResource>
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        /** @var list<User> $admins */
        $admins = $this->queryBus->ask(new ListAdminsQuery());

        return array_map(
            static fn (User $admin): UserResource => UserResource::fromDomain($admin),
            $admins,
        );
    }
}
