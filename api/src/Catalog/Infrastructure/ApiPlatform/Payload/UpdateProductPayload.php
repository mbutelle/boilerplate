<?php

declare(strict_types=1);

namespace App\Catalog\Infrastructure\ApiPlatform\Payload;

use App\Catalog\Application\Command\UpdateProduct\UpdateProductCommand;
use App\Shared\Application\Command\CommandInterface;
use App\Shared\Infrastructure\ApiPlatform\Resource\Payload\CommandableInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class UpdateProductPayload implements CommandableInterface
{
    public function __construct(
        #[Assert\Length(max: 255)]
        public ?string $name = null,
        #[Assert\PositiveOrZero]
        public ?int $priceAmount = null,
        #[Assert\Regex('/^[A-Za-z]{3}$/')]
        public ?string $priceCurrency = null,
        public ?string $description = null,
    ) {
    }

    /**
     * @param array<string, mixed> $uriVariables
     * @param array<string, mixed> $context
     */
    public function toCommand(array $uriVariables, array $context): CommandInterface
    {
        return new UpdateProductCommand(
            (string) ($uriVariables['id'] ?? ''),
            $this->name,
            $this->priceAmount,
            $this->priceCurrency,
            $this->description,
        );
    }
}
