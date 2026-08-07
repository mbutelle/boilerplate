<?php

declare(strict_types=1);

namespace App\IAM\Infrastructure\ApiPlatform\Payload;

use App\IAM\Application\Command\RequestPasswordReset\RequestPasswordResetCommand;
use App\Shared\Application\Command\CommandInterface;
use App\Shared\Infrastructure\ApiPlatform\Resource\Payload\CommandableInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class RequestPasswordResetPayload implements CommandableInterface
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email]
        public ?string $email = null,
    ) {
    }

    /**
     * @param array<string, mixed> $uriVariables
     * @param array<string, mixed> $context
     */
    public function toCommand(array $uriVariables, array $context): CommandInterface
    {
        return new RequestPasswordResetCommand((string) $this->email);
    }
}
