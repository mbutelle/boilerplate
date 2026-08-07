<?php

declare(strict_types=1);

namespace App\IAM\Infrastructure\ApiPlatform\Payload;

use ApiPlatform\Metadata\ApiProperty;
use App\IAM\Application\Command\ChangePassword\ChangePasswordCommand;
use App\Shared\Application\Command\CommandInterface;
use App\Shared\Infrastructure\ApiPlatform\Resource\Payload\CommandableInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class ChangePasswordPayload implements CommandableInterface
{
    public function __construct(
        #[ApiProperty(readable: false)]
        #[Assert\NotBlank]
        #[Assert\Length(min: 8)]
        public ?string $password = null,
    ) {
    }

    /**
     * @param array<string, mixed> $uriVariables
     * @param array<string, mixed> $context
     */
    public function toCommand(array $uriVariables, array $context): CommandInterface
    {
        return new ChangePasswordCommand(
            (string) ($uriVariables['id'] ?? ''),
            (string) $this->password,
        );
    }
}
