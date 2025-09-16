<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Symfony\Translator;

class DummyTranslator implements TranslatorInterface
{
    public function translate(string $message, string $locale): string
    {
        return sprintf('%s %s', $message, $locale);
    }
}
