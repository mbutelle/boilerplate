<?php

declare(strict_types=1);

namespace App\Catalog\Domain\ReadModel;

use App\Catalog\Domain\Model\Product;

/**
 * Une collection répond toujours deux choses : la page demandée et le total.
 * Les transporter ensemble évite que le provider aille chercher le compte par
 * un second chemin.
 */
final class ProductList
{
    /**
     * @param list<Product> $items
     */
    public function __construct(
        public readonly array $items,
        public readonly int $totalItems,
    ) {
    }
}
