<?php

declare(strict_types=1);

namespace App\Security\ApiResource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Security\Entity\User;
use App\Security\State\Processor\CreateAdminProcessor;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/users/admin',
            normalizationContext: ['groups' => ['user:read']],
            denormalizationContext: ['groups' => ['admin:create:write']],
            security: "is_granted('ROLE_ADMIN')",
            output: User::class,
            processor: CreateAdminProcessor::class,
        ),
    ]
)]
class AdminCreationRequest
{
    public function __construct(
        #[Groups(['admin:create:write'])]
        #[ApiProperty(readable: false)]
        public ?string $email = null,
    ) {
    }
}
