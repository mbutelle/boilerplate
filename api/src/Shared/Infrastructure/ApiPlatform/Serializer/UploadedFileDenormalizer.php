<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\ApiPlatform\Serializer;

use Symfony\Component\Cache\Exception\LogicException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class UploadedFileDenormalizer implements DenormalizerInterface
{
    public function denormalize($data, string $type, ?string $format = null, array $context = []): File
    {
        if (!$data instanceof File) {
            throw new LogicException();
        }

        return $data;
    }

    /**
     * @param array<int,mixed> $context
     */
    public function supportsDenormalization($data, $type, $format = null, array $context = []): bool
    {
        return $data instanceof File;
    }

    /**
     * @return array<string,bool>]
     */
    public function getSupportedTypes(?string $format): array
    {
        return [
            File::class => true,
        ];
    }
}
