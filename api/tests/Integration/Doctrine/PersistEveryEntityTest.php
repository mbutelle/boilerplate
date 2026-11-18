<?php

declare(strict_types=1);

use App\Shared\Domain\Contracts\DomainId;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\FieldMapping;

/*
 * Chaque entité mappée s'écrit et se relit dans une vraie base. Les tests
 * unitaires tournent sur des dépôts en mémoire : ils ne voient ni un nom de colonne
 * réservé par MySQL (`rank`), ni un type mal mappé. Les entités sont lues dans
 * les métadonnées de Doctrine : une entité ajoutée plus tard est couverte d'office.
 * Tout se passe dans une transaction annulée à la fin : la base de test reste vide.
 */

/** Une valeur plausible pour un champ, d'après son type Doctrine. */
function sampleValue(FieldMapping $field): mixed
{
    if (null !== $field->enumType) {
        return $field->enumType::cases()[0];
    }

    return match ($field->type) {
        'domain_id' => new DomainId(),
        'string' => substr('valeur-'.bin2hex(random_bytes(4)), 0, max(1, min($field->length ?? 20, 20))),
        'text' => 'un texte',
        'integer', 'smallint', 'bigint' => 3,
        'float' => 1.5,
        'boolean' => true,
        'json' => ['clé' => 'valeur'],
        'datetime' => new DateTime('2026-11-18 10:00:00'),
        'date_immutable' => new DateTimeImmutable('2026-11-18'),
        'datetime_immutable', 'datetime_micro_immutable' => new DateTimeImmutable('2026-11-18 10:00:00'),
        default => throw new LogicException(sprintf('Pas de valeur d\'exemple pour le type « %s » : ajoute-la à ce test.', $field->type)),
    };
}

/**
 * Une instance remplie sans passer par son constructeur : chaque champ mappé, y compris
 * ceux des objets embarqués, reçoit une valeur de son type.
 *
 * @param ClassMetadata<object> $metadata
 */
function sampleEntity(ClassMetadata $metadata): object
{
    $entity = $metadata->newInstance();
    $embedded = [];
    foreach ($metadata->embeddedClasses as $name => $mapping) {
        $embedded[$name] = (new ReflectionClass($mapping->class))->newInstanceWithoutConstructor();
    }

    foreach ($metadata->fieldMappings as $field) {
        $value = sampleValue($field);
        if (null !== $field->declaredField && isset($embedded[$field->declaredField])) {
            (new ReflectionProperty($embedded[$field->declaredField], (string) $field->originalField))->setValue($embedded[$field->declaredField], $value);
            continue;
        }
        (new ReflectionProperty($metadata->getName(), $field->fieldName))->setValue($entity, $value);
    }
    foreach ($embedded as $name => $object) {
        (new ReflectionProperty($metadata->getName(), $name))->setValue($entity, $object);
    }

    return $entity;
}

beforeEach(function (): void {
    self::bootKernel();
    $this->em = self::getContainer()->get(EntityManagerInterface::class);
    $this->em->getConnection()->beginTransaction();
});

afterEach(function (): void {
    $this->em->getConnection()->rollBack();
});

it('écrit et relit chaque entité mappée', function (): void {
    $all = array_filter(
        $this->em->getMetadataFactory()->getAllMetadata(),
        static fn (ClassMetadata $m): bool => !$m->isMappedSuperclass && !$m->isEmbeddedClass,
    );
    expect($all)->not->toBeEmpty();

    $failures = [];
    foreach ($all as $metadata) {
        try {
            $entity = sampleEntity($metadata);
            $this->em->persist($entity);
            $this->em->flush();
            $id = $metadata->getIdentifierValues($entity);
            $this->em->clear();

            $read = $this->em->find($metadata->getName(), $id);
            if (null === $read) {
                $failures[] = sprintf('%s : écrite, mais introuvable à la relecture.', $metadata->getName());
            }
        } catch (Throwable $e) {
            $failures[] = sprintf('%s : %s', $metadata->getName(), mb_substr($e->getMessage(), 0, 300));
            // Une écriture ratée laisse l'EntityManager fermé : on repart d'un neuf pour la suite.
            $this->em->getConnection()->rollBack();
            self::ensureKernelShutdown();
            self::bootKernel();
            $this->em = self::getContainer()->get(EntityManagerInterface::class);
            $this->em->getConnection()->beginTransaction();
        }
    }

    expect($failures)->toBe([]);
});
