<?php

declare(strict_types=1);

use App\Catalog\Domain\ValueObject\Price;

it('expose le montant en centimes et la devise', function (): void {
    $price = Price::fromCents(1250, 'eur');

    expect($price->getAmount())->toBe(1250)
        ->and($price->getCurrency())->toBe('EUR');
});

it('refuse un montant négatif', function (): void {
    Price::fromCents(-1);
})->throws(InvalidArgumentException::class, 'A price cannot be negative.');

it('refuse une devise qui n\'est pas un code ISO à trois lettres', function (): void {
    Price::fromCents(100, 'EURO');
})->throws(InvalidArgumentException::class);

it('accepte le montant zéro, qui est un prix valide', function (): void {
    expect(Price::fromCents(0)->getAmount())->toBe(0);
});

it('compare deux prix sur le montant ET la devise', function (): void {
    $ref = Price::fromCents(1000, 'EUR');

    expect($ref->equals(Price::fromCents(1000, 'EUR')))->toBeTrue()
        ->and($ref->equals(Price::fromCents(1000, 'USD')))->toBeFalse()
        ->and($ref->equals(Price::fromCents(999, 'EUR')))->toBeFalse();
});
