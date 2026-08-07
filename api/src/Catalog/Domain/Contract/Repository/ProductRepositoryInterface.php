<?php

declare(strict_types=1);

namespace App\Catalog\Domain\Contract\Repository;

use App\Catalog\Application\Query\ListProducts\ListProductsQuery;
use App\Catalog\Domain\Model\Product;
use App\Shared\Domain\Contracts\DomainId;

/**
 * buildListQuery() n'apparaît volontairement pas ici : elle renvoie un
 * QueryBuilder, qui n'a rien à faire dans Domain. L'interface n'expose que
 * list() et totalItems().
 */
interface ProductRepositoryInterface
{
    public function get(DomainId $id): ?Product;

    public function findByReference(string $reference): ?Product;

    /**
     * @return list<Product>
     */
    public function list(ListProductsQuery $query): array;

    public function totalItems(ListProductsQuery $query): int;

    public function save(Product $product): void;

    public function remove(Product $product): void;
}
