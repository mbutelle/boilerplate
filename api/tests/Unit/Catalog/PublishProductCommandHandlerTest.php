<?php

declare(strict_types=1);

use App\Catalog\Application\Command\PublishProduct\PublishProductCommand;
use App\Catalog\Application\Command\PublishProduct\PublishProductCommandHandler;
use App\Catalog\Domain\Model\Product;
use App\Catalog\Domain\ValueObject\Price;
use App\Catalog\Domain\ValueObject\ProductStatus;
use App\Tests\Support\InMemoryProductRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/*
 * Le handler ne dépend que de l'interface de dépôt : un double en mémoire suffit,
 * donc ce test tourne sans base ni conteneur. C'est précisément ce que la
 * séparation Domain/Application achète.
 */
it('publie le produit et l\'enregistre', function (): void {
    $product = Product::draft('CAFE-01', 'Café', Price::fromCents(1250));
    $repository = new InMemoryProductRepository([$product]);

    $handler = new PublishProductCommandHandler($repository);
    $handler(new PublishProductCommand((string) $product->getId()));

    expect($product->getStatus())->toBe(ProductStatus::Published)
        ->and($repository->saved)->toHaveCount(1);
});

it('renvoie une 404 quand le produit n\'existe pas', function (): void {
    $handler = new PublishProductCommandHandler(new InMemoryProductRepository([]));

    $handler(new PublishProductCommand('11111111-1111-4111-8111-111111111111'));
})->throws(NotFoundHttpException::class);

it('traduit le refus du domaine en 422', function (): void {
    $product = Product::draft('CAFE-01', 'Café', Price::fromCents(1250));
    $product->archive();

    $handler = new PublishProductCommandHandler(new InMemoryProductRepository([$product]));

    $handler(new PublishProductCommand((string) $product->getId()));
})->throws(UnprocessableEntityHttpException::class);
