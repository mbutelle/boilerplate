<?php

declare(strict_types=1);

namespace App\Catalog\Infrastructure\ApiPlatform\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Catalog\Domain\Contract\Repository\ProductRepositoryInterface;
use App\Catalog\Infrastructure\ApiPlatform\Resource\ProductResource;
use App\Shared\Domain\Contracts\DomainId;
use Symfony\Component\Uid\Uuid;

/**
 * @implements ProviderInterface<ProductResource>
 */
final class ProductItemProvider implements ProviderInterface
{
    public function __construct(
        private ProductRepositoryInterface $repository,
    ) {
    }

    /**
     * @param array<string, mixed> $uriVariables
     * @param array<string, mixed> $context
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?ProductResource
    {
        $id = $uriVariables['id'] ?? null;

        if (!is_string($id) || !Uuid::isValid($id)) {
            return null;
        }

        $product = $this->repository->get(DomainId::fromString($id));

        return null === $product ? null : ProductResource::fromDomain($product);
    }
}
