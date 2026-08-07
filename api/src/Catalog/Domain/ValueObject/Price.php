<?php

declare(strict_types=1);

namespace App\Catalog\Domain\ValueObject;

/**
 * Valeur composite, mappée en embeddable (voir
 * config/doctrine/mapping/Catalog/ValueObject.Price.orm.xml).
 *
 * Le montant est en centimes : un prix en float finit toujours par produire un
 * écart d'arrondi qu'on découvre en facturation.
 */
final class Price
{
    private function __construct(
        private int $amount,
        private string $currency,
    ) {
    }

    public static function fromCents(int $amount, string $currency = 'EUR'): self
    {
        if ($amount < 0) {
            throw new \InvalidArgumentException('A price cannot be negative.');
        }

        if (3 !== strlen($currency)) {
            throw new \InvalidArgumentException('Currency must be a 3-letter ISO 4217 code.');
        }

        return new self($amount, strtoupper($currency));
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function equals(self $other): bool
    {
        return $this->amount === $other->amount && $this->currency === $other->currency;
    }
}
