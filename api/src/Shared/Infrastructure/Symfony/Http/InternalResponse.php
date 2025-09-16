<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Symfony\Http;

use Symfony\Component\HttpFoundation\Response;

final class InternalResponse implements ResponseInterface
{
    public Response $response;

    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    public function getCode(): int
    {
        return $this->response->getStatusCode();
    }

    public function getContent(): string
    {
        return $this->response->getContent();
    }
}
