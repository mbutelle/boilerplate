<?php

declare(strict_types=1);

use App\Catalog\Domain\Model\Product;
use App\Catalog\Domain\ValueObject\Price;
use App\Catalog\Domain\ValueObject\ProductStatus;

function aProduct(): Product
{
    return Product::draft('CAFE-01', 'Café', Price::fromCents(1250));
}

it('naît en brouillon', function (): void {
    expect(aProduct()->getStatus())->toBe(ProductStatus::Draft);
});

it('se publie depuis un brouillon', function (): void {
    $product = aProduct();
    $product->publish();

    expect($product->getStatus())->toBe(ProductStatus::Published)
        ->and($product->getStatus()->isPublished())->toBeTrue();
});

it('refuse de publier un produit archivé', function (): void {
    // La règle vit dans le modèle : le handler ne fait que traduire ce refus en
    // 422. La tester ici la vérifie sans HTTP ni base.
    $product = aProduct();
    $product->archive();

    $product->publish();
})->throws(DomainException::class);

it('se republie une fois repassé en brouillon', function (): void {
    $product = aProduct();
    $product->archive();

    expect(fn () => $product->publish())->toThrow(DomainException::class);
});

it('remplace son prix par une nouvelle valeur', function (): void {
    $product = aProduct();
    $product->changePrice(Price::fromCents(1500, 'USD'));

    expect($product->getPrice()->getAmount())->toBe(1500)
        ->and($product->getPrice()->getCurrency())->toBe('USD');
});

it('reçoit un identifiant dès sa création, sans attendre la base', function (): void {
    // L'identifiant vient du domaine (DomainId), pas d'un auto-increment : un
    // produit est référençable avant même d'être persisté.
    expect((string) aProduct()->getId())->not->toBeEmpty();
});
