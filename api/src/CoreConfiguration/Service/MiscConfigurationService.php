<?php

declare(strict_types=1);

namespace App\CoreConfiguration\Service;

use App\CoreConfiguration\Entity\Global\MiscConfiguration;
use App\CoreConfiguration\Repository\Global\MiscConfigurationRepository;
use Doctrine\ORM\EntityManagerInterface;

class MiscConfigurationService
{
    public function __construct(
        private readonly MiscConfigurationRepository $repository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * Get a configuration value by its key.
     * Returns the default value if the key is not found.
     */
    public function getValue(string $key, string $defaultValue = ''): string
    {
        return $this->repository->getValueByKey($key, $defaultValue);
    }

    /**
     * Get the default jersey weight configuration value.
     */
    public function getDefaultJerseyWeight(float $defaultValue = 0.5): float
    {
        $value = $this->getValue('default_jersey_weight', (string) $defaultValue);

        return (float) $value;
    }

    /**
     * Set a configuration value.
     * Creates a new configuration if the key doesn't exist.
     */
    public function setValue(string $key, string $value, ?string $description = null): MiscConfiguration
    {
        $config = $this->repository->findByKey($key);

        if (!$config) {
            $config = new MiscConfiguration(
                key: $key,
                value: $value,
                description: $description
            );
            $this->entityManager->persist($config);
        } else {
            $config->setValue($value);
            if (null !== $description) {
                $config->setDescription($description);
            }
        }

        $this->entityManager->flush();

        return $config;
    }

    /**
     * Delete a configuration value.
     */
    public function deleteValue(string $key): bool
    {
        $config = $this->repository->findByKey($key);

        if (!$config) {
            return false;
        }

        $this->entityManager->remove($config);
        $this->entityManager->flush();

        return true;
    }
}
