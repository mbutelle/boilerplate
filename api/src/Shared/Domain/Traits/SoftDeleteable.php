<?php

declare(strict_types=1);

namespace App\Shared\Domain\Traits;

use Gedmo\SoftDeleteable\Traits\SoftDeleteable as BaseSoftDeleteable;

trait SoftDeleteable
{
    use BaseSoftDeleteable;
}
