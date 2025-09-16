<?php

declare(strict_types=1);

namespace App\Shared\Domain\Contracts;

interface FileInterface
{
    public function guessExtension(): ?string;

    public function getMimeType(): ?string;

    public function getContent(): string;

    public function getPath(): string;

    public function getFilename(): string;
}
