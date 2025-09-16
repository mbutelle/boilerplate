<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObject\Association;

enum AgreementType: string
{
    public const array CHOICES = [
        self::AGREEMENT_TYPE_CLASSIC->value,
        self::AGREEMENT_TYPE_INTERMEDIATION->value,
        self::AGREEMENT_TYPE_GUARANTEED_MINIMUM->value,
    ];

    case AGREEMENT_TYPE_CLASSIC = 'classic';
    case AGREEMENT_TYPE_INTERMEDIATION = 'intermediation';
    case AGREEMENT_TYPE_GUARANTEED_MINIMUM = 'guaranteed_minimum';
}
