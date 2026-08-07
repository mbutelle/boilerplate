<?php

declare(strict_types=1);

namespace App\Catalog\Application\Command\CreateProduct;

use App\Shared\Application\Command\CommandInterface;

final class CreateProductCommand implements CommandInterface
{
    public function __construct(
        public readonly string $reference,
        public readonly string $name,
        public readonly int $priceAmount,
        public readonly string $priceCurrency = 'EUR',
        public readonly ?string $description = null,
    ) {
    }
}
