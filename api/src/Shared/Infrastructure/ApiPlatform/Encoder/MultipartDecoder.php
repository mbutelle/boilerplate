<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\ApiPlatform\Encoder;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Encoder\DecoderInterface;

final class MultipartDecoder implements DecoderInterface
{
    public const FORMAT = 'multipart';

    public function __construct(private readonly RequestStack $requestStack)
    {
    }

    /**
     * @param mixed[] $context
     *
     * @return mixed[]|null
     *
     * @throws \JsonException
     */
    public function decode(string $data, string $format, array $context = []): ?array
    {
        $request = $this->requestStack->getCurrentRequest();

        if (!$request) {
            return null;
        }

        return array_map(static function (mixed $element) {
            if (!is_string($element)) {
                throw new \InvalidArgumentException('Multipart form values must be strings.');
            }

            try {
                return json_decode($element, true, flags: \JSON_THROW_ON_ERROR);
            } catch (\JsonException $exception) {
                return $element;
            }
        }, $request->request->all()) + $request->files->all();
    }

    public function supportsDecoding(string $format): bool
    {
        return self::FORMAT === $format;
    }
}
