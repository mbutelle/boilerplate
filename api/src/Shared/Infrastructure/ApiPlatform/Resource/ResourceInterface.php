<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\ApiPlatform\Resource;

use App\Shared\Application\Command\CommandInterface;

interface ResourceInterface
{
    /**
     * @param array<string, mixed> $context
     */
    public static function fromDomain(object $domain, array $context = []): self;

    public function getDeleteCommand(): ?CommandInterface;
}
