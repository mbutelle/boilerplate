<?php

declare(strict_types=1);

namespace App\Catalog\Application\Query\ListProducts;

use App\Catalog\Domain\Contract\Repository\ProductRepositoryInterface;
use App\Catalog\Domain\ReadModel\ProductList;
use App\Shared\Application\Query\AsQueryHandler;

#[AsQueryHandler]
final class ListProductsQueryHandler
{
    public function __construct(
        private ProductRepositoryInterface $repository,
    ) {
    }

    public function __invoke(ListProductsQuery $query): ProductList
    {
        return new ProductList(
            $this->repository->list($query),
            $this->repository->totalItems($query),
        );
    }
}
