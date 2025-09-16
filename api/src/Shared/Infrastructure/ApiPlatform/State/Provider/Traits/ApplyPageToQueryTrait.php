<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\ApiPlatform\State\Provider\Traits;

use App\Shared\Application\Query\QueryInterface;

trait ApplyPageToQueryTrait
{
    public function applyPage(QueryInterface $query, array $context): void
    {
        if (!method_exists($query, 'paginate') || !isset($context['filters'])) {
            return;
        }

        $query->paginate(
            isset($context['filters']['page']) ? (int) $context['filters']['page'] : 1,
            isset($context['filters']['itemsPerPage']) ? (int) $context['filters']['itemsPerPage'] : 30,
        );
    }
}
