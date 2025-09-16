<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Symfony\Translator;

use Google\Cloud\Translate\V3\Client\TranslationServiceClient;
use Google\Cloud\Translate\V3\TranslateTextRequest;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[WithMonologChannel('translator.google')]
class GoogleTranslator implements TranslatorInterface
{
    public function __construct(
        #[Autowire('%env(GOOGLE_TRANSLATION_CREDENTIALS)%')]
        private string $googleCredentials,
        #[Autowire('%env(GOOGLE_TRANSLATION_PROJECT_ID)%')]
        private string $googleProjectId,
        private LoggerInterface $logger,
    ) {
    }

    public function translate(string $message, string $locale): string
    {
        try {
            $translate = new TranslationServiceClient([
                'credentials' => $this->googleCredentials,
            ]);

            $request = (new TranslateTextRequest())
                ->setParent(sprintf('projects/%s/locations/global', $this->googleProjectId))
                ->setContents([$message])
                ->setSourceLanguageCode('fr_FR')
                ->setTargetLanguageCode($locale);

            $result = $translate->translateText($request);

            foreach ($result->getTranslations() as $translation) {
                return $translation->getTranslatedText();
            }
        } catch (\Exception $exception) {
            $this->logger->error('Exception while translating message', [
                'message' => $exception->getMessage(),
            ]);
        }

        return $message;
    }
}
