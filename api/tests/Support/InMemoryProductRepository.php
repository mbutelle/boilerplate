<?php

declare(strict_types=1);

namespace App\Tests\Support;

use App\Catalog\Application\Query\ListProducts\ListProductsQuery;
use App\Catalog\Domain\Contract\Repository\ProductRepositoryInterface;
use App\Catalog\Domain\Model\Product;
use App\Shared\Domain\Contracts\DomainId;

/**
 * Double en mémoire du dépôt, pour tester l'Application sans base.
 *
 * Il implémente la même interface que le dépôt Doctrine : si le contrat change,
 * ce double cesse de compiler, ce qui est exactement le signal voulu.
 */
final class InMemoryProductRepository implements ProductRepositoryInterface
{
    /** @var list<Product> */
    public array $saved = [];

    /** @var list<Product> */
    public array $removed = [];

    /**
     * @param list<Product> $products
     */
    public function __construct(private array $products = [])
    {
    }

    public function get(DomainId $id): ?Product
    {
        foreach ($this->products as $product) {
            if ((string) $product->getId() === (string) $id) {
                return $product;
            }
        }

        return null;
    }

    public function findByReference(string $reference): ?Product
    {
        foreach ($this->products as $product) {
            if ($product->getReference() === $reference) {
                return $product;
            }
        }

        return null;
    }

    /**
     * @return list<Product>
     */
    public function list(ListProductsQuery $query): array
    {
        return $this->products;
    }

    public function totalItems(ListProductsQuery $query): int
    {
        return count($this->products);
    }

    public function save(Product $product): void
    {
        $this->saved[] = $product;
    }

    public function remove(Product $product): void
    {
        $this->removed[] = $product;
    }
}
