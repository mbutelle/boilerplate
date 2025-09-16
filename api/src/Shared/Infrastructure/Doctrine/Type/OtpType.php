<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Doctrine\Type;

use App\Shared\Domain\ValueObject\Otp;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Exception\ValueNotConvertible;
use Doctrine\DBAL\Types\JsonType;

class OtpType extends JsonType
{
    public const string NAME = 'otp';

    public function getName(): string
    {
        return self::NAME;
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?Otp
    {
        if (null === $value || '' === $value) {
            return null;
        }

        if (is_resource($value)) {
            $value = stream_get_contents($value);
        }

        try {
            $otp = json_decode($value, true, 512, JSON_THROW_ON_ERROR);

            return new Otp(
                $otp['code'],
                new \DateTimeImmutable($otp['expires_at']),
            );
        } catch (\JsonException $e) {
            throw ValueNotConvertible::new($value, 'json', $e->getMessage(), $e);
        }
    }
}
