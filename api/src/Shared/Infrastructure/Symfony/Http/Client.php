<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Symfony\Http;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class Client
{
    public const string INTERNAL = 'internal';
    public const string EXTERNAL = 'external';

    private HttpClientInterface $client;
    private HttpKernelInterface $httpKernel;

    public function __construct(
        HttpClientInterface $client,
        HttpKernelInterface $httpKernel,
    ) {
        $this->client = $client;
        $this->httpKernel = $httpKernel;
    }

    public function get(string $url, array $queryParameters = [], string $type = self::EXTERNAL): ResponseInterface
    {
        if (self::EXTERNAL === $type) {
            return new ExternalResponse($this->client->request('GET', $url));
        }

        $request = Request::create($url, Request::METHOD_GET, $queryParameters);

        return new InternalResponse(
            $this->httpKernel->handle($request, HttpKernelInterface::SUB_REQUEST)
        );
    }
}
