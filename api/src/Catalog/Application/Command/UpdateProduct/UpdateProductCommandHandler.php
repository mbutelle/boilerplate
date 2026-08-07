<?php

declare(strict_types=1);

namespace App\Catalog\Application\Command\UpdateProduct;

use App\Catalog\Domain\Contract\Repository\ProductRepositoryInterface;
use App\Catalog\Domain\Model\Product;
use App\Catalog\Domain\ValueObject\Price;
use App\Shared\Application\Command\AsCommandHandler;
use App\Shared\Domain\Contracts\DomainId;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[AsCommandHandler]
final class UpdateProductCommandHandler
{
    public function __construct(
        private ProductRepositoryInterface $repository,
    ) {
    }

    public function __invoke(UpdateProductCommand $command): Product
    {
        $product = $this->repository->get(DomainId::fromString($command->productId));

        if (null === $product) {
            throw new NotFoundHttpException('Product not found.');
        }

        if (null !== $command->name) {
            $product->rename($command->name);
        }

        if (null !== $command->priceAmount) {
            $product->changePrice(Price::fromCents(
                $command->priceAmount,
                $command->priceCurrency ?? $product->getPrice()->getCurrency(),
            ));
        }

        if (null !== $command->description) {
            $product->describe($command->description);
        }

        $this->repository->save($product);

        return $product;
    }
}
