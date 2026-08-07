<?php

declare(strict_types=1);

namespace App\Catalog\Infrastructure\ApiPlatform\Resource;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\QueryParameter;
use ApiPlatform\OpenApi\Model\Operation as OpenApiOperation;
use App\Catalog\Application\Command\DeleteProduct\DeleteProductCommand;
use App\Catalog\Domain\Model\Product;
use App\Catalog\Infrastructure\ApiPlatform\Payload\CreateProductPayload;
use App\Catalog\Infrastructure\ApiPlatform\Payload\PublishProductPayload;
use App\Catalog\Infrastructure\ApiPlatform\Payload\UpdateProductPayload;
use App\Catalog\Infrastructure\ApiPlatform\State\Provider\ProductCollectionProvider;
use App\Catalog\Infrastructure\ApiPlatform\State\Provider\ProductItemProvider;
use App\Shared\Application\Command\CommandInterface;
use App\Shared\Infrastructure\ApiPlatform\Resource\ResourceInterface;
use App\Shared\Infrastructure\ApiPlatform\State\Processor\ApplyCommandProcessor;

// Note interne, volontairement PAS en PHPDoc : elle serait publiée comme
// `description` du schéma OpenAPI (voir PasswordResetResource).
//
// Contexte d'exemple : c'est la référence à copier pour un nouveau contexte
// métier. Les cinq écritures passent par ApplyCommandProcessor, les paramètres
// de collection sont déclarés ici — donc présents dans l'OpenAPI, donc dans le
// SDK des fronts.
#[ApiResource(
    shortName: 'Product',
    operations: [
        new GetCollection(
            uriTemplate: '/products',
            openapi: new OpenApiOperation(operationId: 'listProducts'),
            provider: ProductCollectionProvider::class,
            // ApplyPageToQueryTrait lit itemsPerPage dans $context['filters'] :
            // sans cette ligne le paramètre est lu mais jamais publié, donc absent
            // du SDK. Le contrat et le code doivent lire la même chose.
            paginationClientItemsPerPage: true,
            parameters: [
                'query' => new QueryParameter(key: 'query', schema: ['type' => 'string']),
                'status' => new QueryParameter(
                    key: 'status',
                    schema: ['type' => 'string', 'enum' => ['draft', 'published', 'archived']],
                ),
                'sort[name]' => new QueryParameter(
                    key: 'sort[name]',
                    schema: ['type' => 'string', 'enum' => ['ASC', 'DESC']],
                    filter: OrderFilter::class,
                ),
                'sort[reference]' => new QueryParameter(
                    key: 'sort[reference]',
                    schema: ['type' => 'string', 'enum' => ['ASC', 'DESC']],
                    filter: OrderFilter::class,
                ),
                'sort[createdAt]' => new QueryParameter(
                    key: 'sort[createdAt]',
                    schema: ['type' => 'string', 'enum' => ['ASC', 'DESC']],
                    filter: OrderFilter::class,
                ),
            ],
        ),
        new Get(
            uriTemplate: '/products/{id}',
            openapi: new OpenApiOperation(operationId: 'getProduct'),
            provider: ProductItemProvider::class,
        ),
        new Post(
            uriTemplate: '/products',
            openapi: new OpenApiOperation(operationId: 'createProduct'),
            security: "is_granted('ROLE_ADMIN')",
            input: CreateProductPayload::class,
            processor: ApplyCommandProcessor::class,
        ),
        new Patch(
            uriTemplate: '/products/{id}',
            openapi: new OpenApiOperation(operationId: 'updateProduct'),
            security: "is_granted('ROLE_ADMIN')",
            input: UpdateProductPayload::class,
            provider: ProductItemProvider::class,
            processor: ApplyCommandProcessor::class,
        ),
        new Post(
            uriTemplate: '/products/{id}/publish',
            openapi: new OpenApiOperation(operationId: 'publishProduct'),
            security: "is_granted('ROLE_ADMIN')",
            input: PublishProductPayload::class,
            processor: ApplyCommandProcessor::class,
        ),
        new Delete(
            uriTemplate: '/products/{id}',
            openapi: new OpenApiOperation(operationId: 'deleteProduct'),
            security: "is_granted('ROLE_ADMIN')",
            provider: ProductItemProvider::class,
            processor: ApplyCommandProcessor::class,
        ),
    ],
)]
final class ProductResource implements ResourceInterface
{
    public function __construct(
        #[ApiProperty(identifier: true)]
        public ?string $id = null,
        public ?string $reference = null,
        public ?string $name = null,
        public ?string $description = null,
        /** Montant en centimes : la conversion en unité d'affichage est au front. */
        public ?int $priceAmount = null,
        public ?string $priceCurrency = null,
        public ?string $status = null,
        public ?\DateTimeImmutable $createdAt = null,
    ) {
    }

    /**
     * @param array<string, mixed> $context
     */
    public static function fromDomain(object $domain, array $context = []): self
    {
        if (!$domain instanceof Product) {
            throw new \InvalidArgumentException(sprintf('Expected %s, got %s.', Product::class, get_debug_type($domain)));
        }

        $createdAt = $domain->getCreatedAt();

        return new self(
            (string) $domain->getId(),
            $domain->getReference(),
            $domain->getName(),
            $domain->getDescription(),
            $domain->getPrice()->getAmount(),
            $domain->getPrice()->getCurrency(),
            $domain->getStatus()->value,
            $createdAt instanceof \DateTimeInterface
                ? \DateTimeImmutable::createFromInterface($createdAt)
                : null,
        );
    }

    /**
     * Masqué explicitement : sans cela le sérialiseur voit un getter et publie
     * une propriété "deleteCommand" dans le schéma, donc dans le SDK des fronts.
     * Le piège vaut pour toute ressource implémentant ResourceInterface.
     */
    #[ApiProperty(readable: false, writable: false)]
    public function getDeleteCommand(): CommandInterface
    {
        return new DeleteProductCommand((string) $this->id);
    }
}
