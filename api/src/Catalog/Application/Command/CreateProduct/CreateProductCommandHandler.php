<?php

declare(strict_types=1);

namespace App\Catalog\Application\Command\CreateProduct;

use App\Catalog\Domain\Contract\Repository\ProductRepositoryInterface;
use App\Catalog\Domain\Model\Product;
use App\Catalog\Domain\ValueObject\Price;
use App\Shared\Application\Command\AsCommandHandler;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

#[AsCommandHandler]
final class CreateProductCommandHandler
{
    public function __construct(
        private ProductRepositoryInterface $repository,
    ) {
    }

    public function __invoke(CreateProductCommand $command): Product
    {
        if (null !== $this->repository->findByReference($command->reference)) {
            throw new ConflictHttpException(sprintf('A product already exists with reference "%s".', $command->reference));
        }

        $product = Product::draft(
            $command->reference,
            $command->name,
            Price::fromCents($command->priceAmount, $command->priceCurrency),
            $command->description,
        );

        // save() persiste sans flusher : c'est doctrine_transaction qui commite.
        $this->repository->save($product);

        return $product;
    }
}
