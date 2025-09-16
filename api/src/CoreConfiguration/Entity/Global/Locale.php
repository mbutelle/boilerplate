<?php

declare(strict_types=1);

namespace App\CoreConfiguration\Entity\Global;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\CoreConfiguration\Repository\Global\LocaleRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LocaleRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Post(
            security: 'is_granted("ROLE_ADMIN")',
        ),
        new Patch(
            security: 'is_granted("ROLE_ADMIN")',
        ),
        new Delete(
            security: 'is_granted("ROLE_ADMIN")',
        ),
    ]
)]
class Locale
{
    public function __construct(
        #[ORM\Id]
        #[ORM\GeneratedValue(strategy: 'AUTO')]
        #[ORM\Column]
        public ?int $id = null,
        #[ORM\Column(length: 10, unique: true)]
        public ?string $code = null,
        #[ORM\Column(length: 255)]
        public ?string $name = null,
        #[ORM\Column(type: 'boolean')]
        public bool $autoTranslate = true,
    ) {
    }

    public static function create(string $code, string $name, bool $autoTranslate = false): self
    {
        return new self(
            null,
            $code,
            $name,
            $autoTranslate
        );
    }
}
