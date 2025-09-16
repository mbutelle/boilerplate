<?php

declare(strict_types=1);

namespace App\CoreConfiguration\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\QueryParameter;
use ApiPlatform\OpenApi\Model\Parameter;
use App\CoreConfiguration\State\Provider\ExceptionTranslationProvider;
use App\Shared\Application\Command\CommandInterface;
use App\Shared\Infrastructure\ApiPlatform\Resource\ResourceInterface;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    shortName: 'ExceptionTranslation',
    operations: [
        new GetCollection(),
    ],
    routePrefix: '/{_locale}',
    provider: ExceptionTranslationProvider::class,
)]
#[QueryParameter(
    key: '_locale',
    schema: ['type' => 'string'],
    openApi: new Parameter(
        name: '_locale',
        in: 'path',
        description: 'Locale of the resource',
        required: true,
        schema: ['type' => 'string'],
        example: 'fr_FR',
    ),
    required: false
)]
final class ExceptionTranslationResource implements ResourceInterface
{
    public function __construct(
        #[Groups(['read:exception_translation'])]
        public string $key,
        #[Groups(['read:exception_translation'])]
        public string $domain,
        #[Groups(['read:exception_translation'])]
        public string $message,
    ) {
    }

    public static function fromDomain(object|array $domain, array $context = []): self
    {
        if (!is_array($domain)) {
            throw new \InvalidArgumentException(sprintf('Expected array, got %s', get_class($domain)));
        }

        return new self(
            $domain['key'],
            $domain['domain'],
            $domain['message'],
        );
    }

    public function getDeleteCommand(): ?CommandInterface
    {
        return null;
    }
}
