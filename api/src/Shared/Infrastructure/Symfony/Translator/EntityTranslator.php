<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Symfony\Translator;

use App\CoreConfiguration\Repository\Global\LocaleRepository;
use App\Shared\Domain\Contracts\Translatable;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Translatable\Entity\Translation;
use Gedmo\Translatable\TranslatableListener;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

#[WithMonologChannel('auto_translate')]
class EntityTranslator implements EventSubscriberInterface
{
    /**
     * @var Translatable[]
     */
    private array $entitiesToTranslate = [];

    public function __construct(
        private LocaleRepository $localeRepository,
        #[Autowire(service: 'entity_translator')]
        private TranslatorInterface $translator,
        private LoggerInterface $logger,
        private EntityManagerInterface $entityManager,
        #[Autowire(service: 'stof_doctrine_extensions.listener.translatable')]
        private TranslatableListener $translatableListener,
        private PropertyAccessorInterface $propertyAccessor,
    ) {
    }

    public function addEntity(object $entity): void
    {
        if (!$entity instanceof Translatable) {
            return;
        }

        $this->entitiesToTranslate[] = $entity;
    }

    /**
     * @return array<string,string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::TERMINATE => 'translateEntities',
            ConsoleEvents::TERMINATE => 'translateEntities',
        ];
    }

    public function translateEntities(): void
    {
        $this->logger->debug('Translating entities', ['count' => count($this->entitiesToTranslate)]);

        foreach ($this->entitiesToTranslate as $i => $entity) {
            try {
                $this->translate($entity);
            } catch (\Exception $e) {
            }
        }

        if ($this->entityManager->isOpen()) {
            $this->entityManager->flush();
        }
    }

    public function translate(Translatable $entity): void
    {
        $this->logger->debug('Translating entity', ['entity' => get_class($entity)]);
        $translationRepository = $this->entityManager->getRepository(Translation::class);

        $propertiesToTranslate = $this->getPropertiesToTranslate($entity);
        foreach ($this->localeRepository->findBy(['autoTranslate' => true]) as $locale) {
            foreach ($propertiesToTranslate as $property) {
                if (null === $locale->code) {
                    continue;
                }

                $this->logger->debug('Translating property', ['property' => $property, 'locale' => $locale->code]);

                $value = $this->propertyAccessor->getValue($entity, $property);
                if (!is_string($value)) {
                    continue;
                }
                $translationRepository->translate(
                    $entity,
                    $property,
                    $locale->code,
                    $this->translator->translate($value, $locale->code)
                );
            }
        }
    }

    /**
     * @return string[]
     */
    private function getPropertiesToTranslate(Translatable $entity): array
    {
        $config = $this->translatableListener->getConfiguration($this->entityManager, get_class($entity));

        return $config['fields'] ?? [];
    }
}
