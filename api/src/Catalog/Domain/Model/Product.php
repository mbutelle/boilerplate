<?php

declare(strict_types=1);

namespace App\Catalog\Domain\Model;

use App\Catalog\Domain\ValueObject\Price;
use App\Catalog\Domain\ValueObject\ProductStatus;
use App\Shared\Domain\Contracts\DomainId;
use App\Shared\Domain\Traits\Timestampable;

/**
 * Modèle d'exemple. Aucun attribut de mapping ici : le XML vit dans
 * config/doctrine/mapping/Catalog/.
 */
class Product
{
    use Timestampable;

    private ProductStatus $status = ProductStatus::Draft;

    public function __construct(
        private DomainId $id,
        private string $reference,
        private string $name,
        private Price $price,
        private ?string $description = null,
    ) {
        $this->createdAt = new \DateTime();
    }

    public static function draft(string $reference, string $name, Price $price, ?string $description = null): self
    {
        return new self(new DomainId(), $reference, $name, $price, $description);
    }

    public function getId(): DomainId
    {
        return $this->id;
    }

    public function getReference(): string
    {
        return $this->reference;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): Price
    {
        return $this->price;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getStatus(): ProductStatus
    {
        return $this->status;
    }

    public function rename(string $name): void
    {
        $this->name = $name;
    }

    public function changePrice(Price $price): void
    {
        $this->price = $price;
    }

    public function describe(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * Un produit archivé ne repart pas en vente sans repasser par un brouillon :
     * la règle vit dans le modèle, pas dans le handler.
     */
    public function publish(): void
    {
        if (ProductStatus::Archived === $this->status) {
            throw new \DomainException('An archived product must be moved back to draft before being published.');
        }

        $this->status = ProductStatus::Published;
    }

    public function archive(): void
    {
        $this->status = ProductStatus::Archived;
    }
}
