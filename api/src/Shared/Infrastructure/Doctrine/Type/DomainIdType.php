<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Doctrine\Type;

use App\Shared\Domain\Contracts\DomainId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Symfony\Bridge\Doctrine\Types\AbstractUidType;
use Symfony\Component\Uid\AbstractUid;

/**
 * Le type est enregistré sous le nom `domain_id` par config/packages/doctrine.yaml.
 * DBAL 4 a supprimé Type::getName() : le nom ne vit plus que dans cette
 * configuration, plus dans le code PHP.
 */
class DomainIdType extends AbstractUidType
{
    protected function getUidClass(): string
    {
        return DomainId::class;
    }

    /**
     * Force le stockage en chaîne RFC 4122 (UUID natif sur PostgreSQL, CHAR(36)
     * sur MySQL) plutôt que le BINARY(16) que Symfony choisit par défaut sur les
     * plateformes sans type GUID natif.
     *
     * Sans cette surcharge, l'identifiant part en binaire dans une colonne
     * CHAR(36) et MySQL rejette l'écriture avec « Incorrect string value » — une
     * erreur qui ne se manifeste qu'au premier enregistrement réellement créé.
     */
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getGuidTypeDeclarationSQL($column);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if (null === $value || '' === $value) {
            return null;
        }

        if ($value instanceof AbstractUid) {
            return $value->toRfc4122();
        }

        if (\is_string($value)) {
            return $this->getUidClass()::fromString($value)->toRfc4122();
        }

        return parent::convertToDatabaseValue($value, $platform);
    }
}
