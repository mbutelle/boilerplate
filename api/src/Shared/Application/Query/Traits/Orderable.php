<?php

declare(strict_types=1);

namespace App\Shared\Application\Query\Traits;

trait Orderable
{
    public ?string $orderField = null;
    public ?string $orderDirection = null;

    public function order(string $field, string $direction): void
    {
        $this->orderField = $field;
        $this->orderDirection = $direction;
    }

    public function clearOrder(): void
    {
        $this->orderField = null;
        $this->orderDirection = null;
    }
}
