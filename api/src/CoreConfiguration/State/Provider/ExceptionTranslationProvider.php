<?php

declare(strict_types=1);

namespace App\CoreConfiguration\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\CoreConfiguration\Resource\ExceptionTranslationResource;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Yaml\Yaml;

final readonly class ExceptionTranslationProvider implements ProviderInterface
{
    public function __construct(
        private RequestStack $requestStack,
        #[Autowire('%kernel.project_dir%/translations')]
        private string $translationsDir,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $request = $this->requestStack->getCurrentRequest();
        $locale = $request?->get('_locale') ?? 'fr_FR';

        $translations = $this->loadExceptionTranslations($locale);

        // Return all translations
        return array_map(
            fn (array $translation) => ExceptionTranslationResource::fromDomain($translation),
            $translations
        );
    }

    /**
     * @return array<array{key: string, domain: string, message: string}>
     */
    private function loadExceptionTranslations(string $locale): array
    {
        $filename = sprintf('%s/messages+intl-icu.%s.yaml', $this->translationsDir, $locale);

        if (!file_exists($filename)) {
            return [];
        }

        $translations = Yaml::parseFile($filename);
        $exceptionTranslations = [];

        // Process the translations to extract exceptions
        foreach ($translations as $domain => $domainTranslations) {
            $this->extractExceptionTranslations($domain, $domainTranslations, '', $exceptionTranslations);
        }

        return $exceptionTranslations;
    }

    /**
     * Recursively extract exception translations from the YAML structure.
     *
     * @param string                                                     $domain       The top-level domain
     * @param array|string                                               $translations The translations array or string value
     * @param string                                                     $prefix       The current key prefix for nested structures
     * @param array<array{key: string, domain: string, message: string}> $result       The result array to populate
     */
    private function extractExceptionTranslations(string $domain, $translations, string $prefix, array &$result): void
    {
        if (is_array($translations)) {
            // If we're in an 'exception' section or a child of it
            $isExceptionSection = 'exception' === $prefix || str_contains($prefix, '.exception.');

            foreach ($translations as $key => $value) {
                $newPrefix = $prefix ? "$prefix.$key" : $key;

                $this->extractExceptionTranslations($domain, $value, $newPrefix, $result);
            }
        } else {
            $result[] = [
                'key' => "$domain.$prefix",
                'domain' => $domain,
                'message' => $translations,
            ];
        }
    }
}
