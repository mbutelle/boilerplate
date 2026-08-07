<?php

declare(strict_types=1);

namespace App\IAM\Infrastructure\ApiPlatform\Payload;

use ApiPlatform\Metadata\ApiProperty;
use App\IAM\Application\Command\ResetPassword\ResetPasswordCommand;
use App\Shared\Application\Command\CommandInterface;
use App\Shared\Infrastructure\ApiPlatform\Resource\Payload\CommandableInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class ResetPasswordPayload implements CommandableInterface
{
    public const int MIN_LENGTH = 12;
    public const string PATTERN = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).+$/';

    public function __construct(
        #[ApiProperty(readable: false)]
        #[Assert\NotBlank]
        #[Assert\Length(min: self::MIN_LENGTH)]
        // Une seule expression à lookaheads plutôt que quatre contraintes :
        // OpenAPI n'expose qu'un `pattern` par propriété, et c'est lui qui
        // devient la règle zod du front. Quatre Regex n'en publieraient qu'une,
        // et le front revaliderait à l'aveugle les trois autres.
        #[Assert\Regex(
            pattern: self::PATTERN,
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
        return new ResetPasswordCommand(
            (string) ($uriVariables['token'] ?? ''),
            (string) $this->password,
        );
    }
}
