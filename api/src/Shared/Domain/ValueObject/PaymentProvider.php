<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

enum PaymentProvider: string
{
    case STRIPE = 'stripe';
}
