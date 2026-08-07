<?php

declare(strict_types=1);

namespace App\Catalog\Application\Command\UpdateProduct;

use App\Shared\Application\Command\CommandInterface;

final class UpdateProductCommand implements CommandInterface
{
    public function __construct(
        public readonly string $productId,
        public readonly ?string $name = null,
        public readonly ?int $priceAmount = null,
        public readonly ?string $priceCurrency = null,
        public readonly ?string $description = null,
    ) {
    }
}
