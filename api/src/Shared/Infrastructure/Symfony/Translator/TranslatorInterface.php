<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Symfony\Translator;

interface TranslatorInterface
{
    public function translate(string $message, string $locale): string;
}
