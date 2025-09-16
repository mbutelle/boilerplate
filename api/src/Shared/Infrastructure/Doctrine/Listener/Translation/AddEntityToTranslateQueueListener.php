<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Doctrine\Listener\Translation;

use App\Shared\Domain\Contracts\Translatable;
use App\Shared\Infrastructure\Symfony\Translator\EntityTranslator;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Monolog\Attribute\WithMonologChannel;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RequestStack;

#[AsDoctrineListener(event: Events::prePersist)]
#[AsDoctrineListener(event: Events::postUpdate)]
#[WithMonologChannel('auto_translate')]
final readonly class AddEntityToTranslateQueueListener
{
    public function __construct(
        private EntityTranslator $entityTranslator,
        private RequestStack $requestStack,
        #[Autowire(param: 'kernel.default_locale')]
        private string $defaultLocale,
    ) {
    }

    /**
     * @param LifecycleEventArgs<EntityManager> $args
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        $this->translate($args);
    }

    /**
     * @param LifecycleEventArgs<EntityManager> $args
     */
    public function postUpdate(LifecycleEventArgs $args): void
    {
        $this->translate($args);
    }

    /**
     * @param LifecycleEventArgs<EntityManager> $args
     */
    public function translate(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Translatable) {
            return;
        }

        if (null === $request = $this->requestStack->getMainRequest()) {
            $this->entityTranslator->translate($entity);

            return;
        }

        if (!$this->isDefaultLocale($request->getLocale())) {
            return;
        }

        $this->entityTranslator->addEntity($entity);
    }

    private function isDefaultLocale(string $locale): bool
    {
        return $locale === $this->defaultLocale;
    }
}
