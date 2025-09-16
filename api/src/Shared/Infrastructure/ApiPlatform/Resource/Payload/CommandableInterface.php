<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\ApiPlatform\Resource\Payload;

use App\Shared\Application\Command\CommandInterface;

interface CommandableInterface
{
    /**
     * @param mixed[] $uriVariables
     * @param mixed[] $context
     */
    public function toCommand(array $uriVariables, array $context): CommandInterface;
}
