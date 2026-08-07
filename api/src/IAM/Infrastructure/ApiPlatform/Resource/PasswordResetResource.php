<?php

declare(strict_types=1);

namespace App\IAM\Infrastructure\ApiPlatform\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\IAM\Infrastructure\ApiPlatform\Payload\RequestPasswordResetPayload;
use App\IAM\Infrastructure\ApiPlatform\Payload\ResetPasswordPayload;
use App\Shared\Infrastructure\ApiPlatform\State\Processor\ApplyCommandProcessor;

/**
 * Ressource sans modèle de domaine derrière : les deux opérations ne projettent
 * rien (204), elles ne font que dispatcher une commande. C'est pour cette raison
 * qu'elle n'implémente pas ResourceInterface — il n'y a pas de fromDomain() qui
 * ait un sens ici.
 */
#[ApiResource(
    shortName: 'PasswordReset',
    operations: [
        new Post(
            uriTemplate: '/users/password-reset-requests/send',
            status: 204,
            input: RequestPasswordResetPayload::class,
            output: false,
            processor: ApplyCommandProcessor::class,
        ),
        new Post(
            uriTemplate: '/users/password-reset-requests/{token}/apply',
            status: 204,
            input: ResetPasswordPayload::class,
            output: false,
            read: false,
            processor: ApplyCommandProcessor::class,
        ),
    ],
)]
final class PasswordResetResource
{
}
