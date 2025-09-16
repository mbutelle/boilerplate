<?php

declare(strict_types=1);

namespace App\CoreConfiguration\Repository\CMS\FAQ;

use App\CoreConfiguration\Entity\CMS\FAQ\FaqEntry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FaqEntry>
 */
class FaqEntryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FaqEntry::class);
    }
}
