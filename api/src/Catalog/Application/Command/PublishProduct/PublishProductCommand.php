<?php

declare(strict_types=1);

namespace App\Catalog\Application\Command\PublishProduct;

use App\Shared\Application\Command\CommandInterface;

final class PublishProductCommand implements CommandInterface
{
    public function __construct(
        public readonly string $productId,
    ) {
    }
}
