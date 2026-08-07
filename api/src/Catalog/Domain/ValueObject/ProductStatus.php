<?php

declare(strict_types=1);

namespace App\Catalog\Domain\ValueObject;

/**
 * Les valeurs de backing sont le contrat stocké en base : elles se choisissent
 * lisibles une fois, et ne se renomment plus ensuite.
 */
enum ProductStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Archived = 'archived';

    public function isPublished(): bool
    {
        return self::Published === $this;
    }
}
