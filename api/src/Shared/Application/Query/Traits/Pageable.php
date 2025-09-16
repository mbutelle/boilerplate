<?php

declare(strict_types=1);

namespace App\Shared\Application\Query\Traits;

trait Pageable
{
    public ?int $page = 1;
    public ?int $limit = 30;

    public function paginate(?int $page, ?int $limit): void
    {
        $this->page = $page;
        $this->limit = $limit;
    }

    public function clearPagination(): void
    {
        $this->page = null;
        $this->limit = null;
    }
}
