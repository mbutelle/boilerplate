<?php

declare(strict_types=1);

namespace App\Security\ApiResource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Security\State\Processor\UserPasswordResetProcessor;
use App\Security\State\Processor\UserPasswordResetRequestProcessor;
use App\Security\State\Provider\UserByResetTokenProvider;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/users/password-reset-requests/send',
            denormalizationContext: ['groups' => ['password-reset-requests:write']],
            output: false,
            processor: UserPasswordResetRequestProcessor::class
        ),
        new Patch(
            uriTemplate: '/users/password-reset-requests/{token}/apply',
            denormalizationContext: ['groups' => ['reset_password:write']],
            output: false,
            provider: UserByResetTokenProvider::class,
            processor: UserPasswordResetProcessor::class
        ),
    ]
)]
class PasswordResetRequest
{
    public function __construct(
        #[ApiProperty(writable: false, identifier: true)]
        public ?string $token = null,
        #[Groups(['password-reset-requests:write'])]
        #[ApiProperty(writable: false)]
        public ?string $email = null,
        #[Groups(['reset_password:write'])]
        #[ApiProperty(readable: false)]
        public ?string $password = null,
    ) {
    }
}
