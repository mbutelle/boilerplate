<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\ApiPlatform\State\Provider\Traits;

use App\Shared\Application\Query\QueryInterface;

trait ApplyOrderToQueryTrait
{
    public function applyOrder(QueryInterface $query, array $context): void
    {
        if (!method_exists($query, 'order') || !isset($context['filters'])) {
            return;
        }

        if (isset($context['filters']['sort']) && count($context['filters']['sort']) > 0) {
            $query->order(
                array_key_first($context['filters']['sort']),
                array_values($context['filters']['sort'])[0],
            );
        }
    }
}
