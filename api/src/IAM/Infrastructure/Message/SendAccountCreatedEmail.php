<?php

declare(strict_types=1);

namespace App\IAM\Infrastructure\Message;

use App\Shared\Infrastructure\Message\MessageInterface;

/**
 * Message asynchrone : il ne transporte qu'un identifiant, jamais l'entité.
 *
 * C'est la règle qui rend l'asynchrone sûr — un modèle de domaine sérialisé
 * puis désérialisé par le worker serait détaché de l'unité de travail Doctrine,
 * et travaillerait sur un état figé au moment de l'envoi.
 */
final class SendAccountCreatedEmail implements MessageInterface
{
    public function __construct(
        public readonly string $userId,
    ) {
    }
}
