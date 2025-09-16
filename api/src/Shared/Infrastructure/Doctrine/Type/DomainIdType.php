<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Doctrine\Type;

use App\Shared\Domain\Contracts\DomainId;
use Symfony\Bridge\Doctrine\Types\AbstractUidType;

class DomainIdType extends AbstractUidType
{
    public const string NAME = 'domain_id';

    public function getName(): string
    {
        return self::NAME;
    }

    protected function getUidClass(): string
    {
        return DomainId::class;
    }
}
