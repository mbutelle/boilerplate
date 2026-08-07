<?php

declare(strict_types=1);

namespace App\IAM\Infrastructure\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\Model\MediaType;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\PathItem;
use ApiPlatform\OpenApi\Model\RequestBody;
use ApiPlatform\OpenApi\Model\Response as OpenApiResponse;
use ApiPlatform\OpenApi\OpenApi;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\HttpFoundation\Response;

/**
 * Documente les deux opérations que le bundle de sécurité expose hors API
 * Platform : /auth et le rafraîchissement de jeton. Sans l'attribut de
 * décoration ci-dessous la classe n'est jamais appelée, et ces routes
 * n'existent ni dans l'OpenAPI ni dans le SDK des fronts.
 */
// Priorité basse = décorateur le plus externe, donc appliqué en dernier. C'est
// nécessaire : /auth est ajouté par le décorateur du bundle JWT, et un
// décorateur appliqué avant lui ne verrait pas encore la route.
#[AsDecorator('api_platform.openapi.factory', priority: -100)]
final class OpenApiFactory implements OpenApiFactoryInterface
{
    private OpenApiFactoryInterface $decorated;

    public function __construct(OpenApiFactoryInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = $this->decorated->__invoke($context);

        $authPath = $openApi->getPaths()->getPath('/auth');
        $authPost = $authPath?->getPost();

        // Sans garde, un socle dont le pare-feu n'expose pas /auth ferait échouer
        // l'export entier de l'OpenAPI.
        $authPost?->addResponse((new OpenApiResponse())
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
