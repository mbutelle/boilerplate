<?php

declare(strict_types=1);

namespace App\Catalog\Infrastructure\ApiPlatform\Payload;

use App\Catalog\Application\Command\PublishProduct\PublishProductCommand;
use App\Shared\Application\Command\CommandInterface;
use App\Shared\Infrastructure\ApiPlatform\Resource\Payload\CommandableInterface;

/**
 * Payload sans corps : l'action ne prend que l'identifiant de l'URI. Il existe
 * quand même, pour que l'opération reste un cas ordinaire du pattern plutôt
 * qu'un processor écrit à la main.
 */
final class PublishProductPayload implements CommandableInterface
{
    /**
     * @param array<string, mixed> $uriVariables
     * @param array<string, mixed> $context
     */
    public function toCommand(array $uriVariables, array $context): CommandInterface
    {
        return new PublishProductCommand((string) ($uriVariables['id'] ?? ''));
    }
}
