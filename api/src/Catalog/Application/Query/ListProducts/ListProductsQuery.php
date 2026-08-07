<?php

declare(strict_types=1);

namespace App\Catalog\Application\Query\ListProducts;

use App\Catalog\Domain\ValueObject\ProductStatus;
use App\Shared\Application\Query\QueryInterface;
use App\Shared\Application\Query\Traits\Orderable;
use App\Shared\Application\Query\Traits\Pageable;

final class ListProductsQuery implements QueryInterface
{
    use Orderable;
    use Pageable;

    public ?string $search = null;
    public ?ProductStatus $status = null;

    public function withSearch(?string $search): self
    {
        $this->search = '' === $search ? null : $search;

        return $this;
    }

    public function withStatus(?ProductStatus $status): self
    {
        $this->status = $status;

        return $this;
    }
}
