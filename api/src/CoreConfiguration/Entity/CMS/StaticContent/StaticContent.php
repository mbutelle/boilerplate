<?php

declare(strict_types=1);

namespace App\CoreConfiguration\Entity\CMS\StaticContent;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\QueryParameter;
use ApiPlatform\OpenApi\Model\Parameter;
use App\CoreConfiguration\Repository\CMS\StaticContent\StaticContentRepository;
use App\Shared\Domain\Contracts\Translatable;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: StaticContentRepository::class)]
#[ORM\Table(name: 'core_cms_static_content')]
#[ORM\UniqueConstraint(name: 'uniq_core_cms_static_content_code', columns: ['code'])]
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
class StaticContent implements Translatable
{
    public function __construct(
        #[ORM\Id]
        #[ORM\GeneratedValue(strategy: 'AUTO')]
        #[ORM\Column]
        public ?int $id = null,

        // Business key to identify the kind of static content (e.g., CGU, CGV, ABOUT, PRIVACY)
        #[ORM\Column(type: 'string', length: 120, nullable: false)]
        public string $code = '',

        // Optional human-friendly title
        #[Gedmo\Translatable]
        #[ORM\Column(type: 'string', length: 255, nullable: true)]
        public ?string $title = null,

        // The actual HTML content
        #[Gedmo\Translatable]
        #[ORM\Column(type: 'text', nullable: true)]
        public ?string $content = null,
    ) {
    }
}
