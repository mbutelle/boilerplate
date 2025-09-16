<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Message\Account;

use App\Account\Domain\Model\Account;

class AccountDeleted
{
    public function __construct(
        public Account $account,
    ) {
    }
}
