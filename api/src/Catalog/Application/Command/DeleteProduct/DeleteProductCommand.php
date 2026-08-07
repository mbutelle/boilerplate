<?php

declare(strict_types=1);

namespace App\Catalog\Application\Command\DeleteProduct;

use App\Shared\Application\Command\CommandInterface;

final class DeleteProductCommand implements CommandInterface
{
    public function __construct(
        public readonly string $productId,
    ) {
    }
}
