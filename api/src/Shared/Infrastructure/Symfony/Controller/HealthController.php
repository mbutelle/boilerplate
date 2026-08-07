<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Symfony\Controller;

use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Sonde de vivacité, utilisée par la healthcheck Docker et destinée aux probes
 * de l'orchestrateur en production.
 *
 * Elle vérifie la base : un conteneur qui répond mais ne peut plus joindre sa
 * base est en panne, même si le serveur HTTP va bien.
 */
final class HealthController
{
    #[Route('/health', name: 'health', methods: ['GET'])]
    public function __invoke(Connection $connection): JsonResponse
    {
        try {
            $connection->executeQuery('SELECT 1');
        } catch (\Throwable $e) {
            return new JsonResponse(
                ['status' => 'error', 'database' => $e->getMessage()],
                Response::HTTP_SERVICE_UNAVAILABLE,
            );
        }

        return new JsonResponse(['status' => 'ok']);
    }
}
