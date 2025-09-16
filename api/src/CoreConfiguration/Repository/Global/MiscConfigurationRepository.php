<?php

declare(strict_types=1);

namespace App\CoreConfiguration\Repository\Global;

use App\CoreConfiguration\Entity\Global\MiscConfiguration;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MiscConfiguration>
 */
class MiscConfigurationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MiscConfiguration::class);
    }

    /**
     * Find a configuration value by its key.
     */
    public function findByKey(string $key): ?MiscConfiguration
    {
        return $this->createQueryBuilder('mc')
            ->where('mc.key = :key')
            ->setParameter('key', $key)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Get a configuration value by its key.
     * Returns the default value if the key is not found.
     */
    public function getValueByKey(string $key, string $defaultValue = ''): string
    {
        $config = $this->findByKey($key);

        return $config ? $config->getValue() : $defaultValue;
    }

    /**
     * Find all configuration values ordered by key.
     */
    public function findAllOrdered(): array
    {
        return $this->createQueryBuilder('mc')
            ->orderBy('mc.key', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
