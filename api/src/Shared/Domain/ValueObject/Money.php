<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

final readonly class Money
{
    private function __construct(
        private int $amount,
        private string $currency = 'EUR',
    ) {
        $this->ensureIsValidAmount($amount);
    }

    public static function fromAmount(int $amount, string $currency = 'EUR'): self
    {
        return new self($amount, $currency);
    }

    public static function fromDisplayAmount(float $amount, string $currency = 'EUR'): self
    {
        return new self((int) round($amount * 100), $currency);
    }

    public function amount(): int
    {
        return $this->amount;
    }

    public function currency(): string
    {
        return $this->currency;
    }

    public function equals(self $other): bool
    {
        return $this->amount === $other->amount && $this->currency === $other->currency;
    }

    public function isGreaterThan(self $other): bool
    {
        $this->ensureSameCurrency($other);

        return $this->amount > $other->amount;
    }

    public function isGreaterOrEquals(self $other): bool
    {
        $this->ensureSameCurrency($other);

        return $this->amount >= $other->amount;
    }

    public function isLessThan(self $other): bool
    {
        $this->ensureSameCurrency($other);

        return $this->amount < $other->amount;
    }

    public function isLessOrEquals(self $other): bool
    {
        $this->ensureSameCurrency($other);

        return $this->amount <= $other->amount;
    }

    public function add(self $other): self
    {
        $this->ensureSameCurrency($other);

        return new self($this->amount + $other->amount, $this->currency);
    }

    public function subtract(self $other): self
    {
        $this->ensureSameCurrency($other);

        return new self($this->amount - $other->amount, $this->currency);
    }

    public function multiply(int $multiplier): self
    {
        return new self($this->amount * $multiplier, $this->currency);
    }

    public function divide(float $divider): self
    {
        if ($divider < 0) {
            throw new \InvalidArgumentException('Division by zero');
        }

        return self::fromAmount((int) round($this->amount / $divider), $this->currency);
    }

    public function getPercent(float $percent): self
    {
        return new self((int) round($this->amount * $percent / 100), $this->currency);
    }

    public function toString(): string
    {
        return sprintf('%d %s', $this->amount, $this->currency);
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function displayAmount(): float
    {
        return round($this->amount / 100, 2);
    }

    private function ensureIsValidAmount(int $amount): void
    {
        if ($amount < 0) {
            throw new \InvalidArgumentException('Amount cannot be negative');
        }
    }

    private function ensureSameCurrency(self $other): void
    {
        if ($this->currency !== $other->currency) {
            throw new \InvalidArgumentException(sprintf('Cannot compare money with different currencies: %s and %s', $this->currency, $other->currency));
        }
    }
}
