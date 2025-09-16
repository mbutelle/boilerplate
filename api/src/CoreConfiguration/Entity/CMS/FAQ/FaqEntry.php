<?php

declare(strict_types=1);

namespace App\CoreConfiguration\Entity\CMS\FAQ;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\QueryParameter;
use ApiPlatform\OpenApi\Model\Parameter;
use App\CoreConfiguration\Repository\CMS\FAQ\FaqEntryRepository;
use App\Shared\Domain\Contracts\Translatable;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: FaqEntryRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Post(
            security: 'is_granted("ROLE_ADMIN")',
        ),
        new Put(
            security: 'is_granted("ROLE_ADMIN")',
        ),
        new Delete(
            security: 'is_granted("ROLE_ADMIN")',
        ),
    ],
    routePrefix: '/{_locale}',
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
class FaqEntry implements Translatable
{
    public function __construct(
        #[ORM\Id]
        #[ORM\GeneratedValue(strategy: 'AUTO')]
        #[ORM\Column]
        public ?int $id = null,
        #[Gedmo\Translatable]
        #[ORM\Column(type: 'text')]
        public ?string $question = null,
        #[Gedmo\Translatable]
        #[ORM\Column(type: 'text')]
        public ?string $answer = null,
    ) {
    }
}
