<?php

declare(strict_types=1);

namespace App\CoreConfiguration\Repository\CMS\StaticContent;

use App\CoreConfiguration\Entity\CMS\StaticContent\StaticContent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StaticContent>
 */
class StaticContentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StaticContent::class);
    }
}
