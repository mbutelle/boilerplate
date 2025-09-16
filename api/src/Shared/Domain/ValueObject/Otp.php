<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

class Otp implements \JsonSerializable
{
    private const OTP_LENGTH = 6;
    private const OTP_EXPIRY_MINUTES = 5;

    public function __construct(
        private readonly string $code,
        private readonly \DateTimeImmutable $expiresAt,
    ) {
    }

    public static function generate(): self
    {
        $code = '';
        for ($i = 0; $i < self::OTP_LENGTH; ++$i) {
            $code .= random_int(0, 9);
        }

        return new self(
            $code,
            (new \DateTimeImmutable())->modify(sprintf('+%d minutes', self::OTP_EXPIRY_MINUTES))
        );
    }

    public function isExpired(): bool
    {
        return $this->expiresAt < new \DateTimeImmutable();
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getExpiresAt(): \DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function jsonSerialize(): array
    {
        return [
            'code' => $this->code,
            'expires_at' => $this->expiresAt->format('Y-m-d\TH:i:s.uP'),
        ];
    }
}
