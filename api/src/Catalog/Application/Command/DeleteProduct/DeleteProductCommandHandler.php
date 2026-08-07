<?php

declare(strict_types=1);

namespace App\Catalog\Application\Command\DeleteProduct;

use App\Catalog\Domain\Contract\Repository\ProductRepositoryInterface;
use App\Shared\Application\Command\AsCommandHandler;
use App\Shared\Domain\Contracts\DomainId;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[AsCommandHandler]
final class DeleteProductCommandHandler
{
    public function __construct(
        private ProductRepositoryInterface $repository,
    ) {
    }

    public function __invoke(DeleteProductCommand $command): void
    {
        $product = $this->repository->get(DomainId::fromString($command->productId));

        if (null === $product) {
            throw new NotFoundHttpException('Product not found.');
        }

        $this->repository->remove($product);
    }
}
