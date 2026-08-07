<?php

declare(strict_types=1);

namespace App\Catalog\Application\Command\PublishProduct;

use App\Catalog\Domain\Contract\Repository\ProductRepositoryInterface;
use App\Catalog\Domain\Model\Product;
use App\Shared\Application\Command\AsCommandHandler;
use App\Shared\Domain\Contracts\DomainId;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

#[AsCommandHandler]
final class PublishProductCommandHandler
{
    public function __construct(
        private ProductRepositoryInterface $repository,
    ) {
    }

    public function __invoke(PublishProductCommand $command): Product
    {
        $product = $this->repository->get(DomainId::fromString($command->productId));

        if (null === $product) {
            throw new NotFoundHttpException('Product not found.');
        }

        try {
            $product->publish();
        } catch (\DomainException $e) {
            // La règle appartient au modèle ; le handler ne fait que traduire son
            // refus en réponse HTTP.
            throw new UnprocessableEntityHttpException($e->getMessage(), $e);
        }

        $this->repository->save($product);

        return $product;
    }
}
