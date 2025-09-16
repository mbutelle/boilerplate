<?php

declare(strict_types=1);

namespace App\Security\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\Model\MediaType;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\PathItem;
use ApiPlatform\OpenApi\Model\RequestBody;
use ApiPlatform\OpenApi\Model\Response as OpenApiResponse;
use ApiPlatform\OpenApi\OpenApi;
use Symfony\Component\HttpFoundation\Response;

final class OpenApiFactory implements OpenApiFactoryInterface
{
    private $decorated;

    public function __construct(OpenApiFactoryInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = $this->decorated->__invoke($context);

        $openApi
            ->getPaths()
            ->getPath('/auth')
            ->getPost()
            ->addResponse((new OpenApiResponse())
                ->withDescription('Login')
                ->withContent(new \ArrayObject([
                    'application/json' => [
                        'schema' => [
                            'type' => 'object',
                            'properties' => [
                                'token' => [
                                    'readOnly' => true,
                                    'type' => 'string',
                                    'nullable' => false,
                                ],
                                'refresh_token' => [
                                    'readOnly' => true,
                                    'type' => 'string',
                                    'nullable' => false,
                                ],
                            ],
                            'required' => ['token', 'refresh_token'],
                        ],
                    ],
                ])),
                Response::HTTP_OK
            )
        ;

        $openApi
            ->getPaths()
            ->addPath('/api/token/refresh', (new PathItem())->withPost(
                (new Operation())
                    ->withOperationId('gesdinet_jwt_refresh_token')
                    ->withTags(['Refresh token'])
                    ->withResponses([
                        Response::HTTP_OK => [
                            'description' => 'Refresh token',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'token' => [
                                                'readOnly' => true,
                                                'type' => 'string',
                                                'nullable' => false,
                                            ],
                                            'refresh_token' => [
                                                'readOnly' => true,
                                                'type' => 'string',
                                                'nullable' => false,
                                            ],
                                        ],
                                        'required' => ['token', 'refresh_token'],
                                    ],
                                ],
                            ],
                        ],
                    ])
                    ->withSummary('Refresh token.')
                    ->withDescription('Refresh token.')
                    ->withRequestBody(
                        (new RequestBody())
                            ->withDescription('Refresh token')
                            ->withContent(new \ArrayObject([
                                'application/json' => new MediaType(new \ArrayObject(new \ArrayObject([
                                    'type' => 'object',
                                    'properties' => [
                                        'refresh_token' => [
                                            'type' => 'string',
                                            'nullable' => false,
                                        ],
                                    ],
                                    'required' => ['refresh_token'],
                                ]))),
                            ]))
                            ->withRequired(true)
                    )
            ))
        ;

        return $openApi;
    }
}
