<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Symfony\HttpFoundation;

use App\Shared\Domain\Contracts\FileInterface;
use Symfony\Component\HttpFoundation\File\File as Base;

class File extends Base implements FileInterface
{
}
