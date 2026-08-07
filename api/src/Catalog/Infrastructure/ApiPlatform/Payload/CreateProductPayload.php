<?php

declare(strict_types=1);

namespace App\Catalog\Infrastructure\ApiPlatform\Payload;

use App\Catalog\Application\Command\CreateProduct\CreateProductCommand;
use App\Shared\Application\Command\CommandInterface;
use App\Shared\Infrastructure\ApiPlatform\Resource\Payload\CommandableInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class CreateProductPayload implements CommandableInterface
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(max: 64)]
        public ?string $reference = null,
        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        public ?string $name = null,
        #[Assert\NotNull]
        #[Assert\PositiveOrZero]
        public ?int $priceAmount = null,
        // Assert\Currency exigerait symfony/intl : une expression suffit pour un
        // code ISO 4217, et Price::fromCents() reste le garde-fou final.
        #[Assert\Regex('/^[A-Za-z]{3}$/')]
        public string $priceCurrency = 'EUR',
        public ?string $description = null,
    ) {
    }

    /**
     * @param array<string, mixed> $uriVariables
     * @param array<string, mixed> $context
     */
    public function toCommand(array $uriVariables, array $context): CommandInterface
    {
        return new CreateProductCommand(
            (string) $this->reference,
            (string) $this->name,
            (int) $this->priceAmount,
            $this->priceCurrency,
            $this->description,
        );
    }
}
