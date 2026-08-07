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
        // Mêmes exigences que la réinitialisation : la règle est définie une fois
        // et réutilisée, sinon les deux chemins de changement de mot de passe
        // finissent par diverger.
        #[Assert\Length(min: ResetPasswordPayload::MIN_LENGTH)]
        #[Assert\Regex(
            pattern: ResetPasswordPayload::PATTERN,
            message: 'Le mot de passe doit contenir une majuscule, une minuscule, un chiffre et un caractère spécial.',
        )]
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
