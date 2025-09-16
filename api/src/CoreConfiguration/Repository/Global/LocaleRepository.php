<?php

declare(strict_types=1);

namespace App\CoreConfiguration\Repository\Global;

use App\CoreConfiguration\Entity\Global\Locale;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Locale>
 */
class LocaleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Locale::class);
    }
}
