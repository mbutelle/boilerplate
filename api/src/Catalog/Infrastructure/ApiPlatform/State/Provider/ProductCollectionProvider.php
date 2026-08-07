<?php

declare(strict_types=1);

namespace App\Catalog\Infrastructure\ApiPlatform\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\TraversablePaginator;
use ApiPlatform\State\ProviderInterface;
use App\Catalog\Application\Query\ListProducts\ListProductsQuery;
use App\Catalog\Domain\Model\Product;
use App\Catalog\Domain\ReadModel\ProductList;
use App\Catalog\Domain\ValueObject\ProductStatus;
use App\Catalog\Infrastructure\ApiPlatform\Resource\ProductResource;
use App\Shared\Application\Query\QueryBusInterface;
use App\Shared\Infrastructure\ApiPlatform\State\Provider\Traits\ApplyOrderToQueryTrait;
use App\Shared\Infrastructure\ApiPlatform\State\Provider\Traits\ApplyPageToQueryTrait;

/**
 * Le paramètre générique est `object` et non ProductResource : l'opération rend
 * un paginateur, pas une ressource isolée.
 *
 * @implements ProviderInterface<object>
 */
final class ProductCollectionProvider implements ProviderInterface
{
    use ApplyOrderToQueryTrait;
    use ApplyPageToQueryTrait;

    public function __construct(
        private QueryBusInterface $queryBus,
    ) {
    }

    /**
     * @param array<string, mixed> $uriVariables
     * @param array<string, mixed> $context
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object
    {
        $query = new ListProductsQuery();

        // Les paramètres se lisent dans $context['filters'], jamais dans la
        // Request : c'est la seule source qui corresponde à ce qui est déclaré
        // sur l'opération, donc à ce que le SDK expose.
        $this->applyOrder($query, $context);
        $this->applyPage($query, $context);

        $filters = $context['filters'] ?? [];

        if (isset($filters['query']) && is_string($filters['query'])) {
            $query->withSearch($filters['query']);
        }

        if (isset($filters['status']) && is_string($filters['status'])) {
            $query->withStatus(ProductStatus::tryFrom($filters['status']));
        }

        /** @var ProductList $result */
        $result = $this->queryBus->ask($query);

        $resources = array_map(
            static fn (Product $product): ProductResource => ProductResource::fromDomain($product),
            $result->items,
        );

        return new TraversablePaginator(
            new \ArrayIterator($resources),
            $query->page ?? 1,
            $query->limit ?? 30,
            $result->totalItems,
        );
    }
}
