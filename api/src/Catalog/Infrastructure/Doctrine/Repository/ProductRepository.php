<?php

declare(strict_types=1);

namespace App\Catalog\Infrastructure\Doctrine\Repository;

use App\Catalog\Application\Query\ListProducts\ListProductsQuery;
use App\Catalog\Domain\Contract\Repository\ProductRepositoryInterface;
use App\Catalog\Domain\Model\Product;
use App\Shared\Domain\Contracts\DomainId;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository implements ProductRepositoryInterface
{
    /**
     * Liste blanche du tri : un champ arbitraire venu de l'URL ne doit jamais
     * atteindre le ORDER BY. Un champ inconnu retombe sur le tri par défaut —
     * une liste triée autrement, jamais une erreur 500.
     *
     * @var array<string, string>
     */
    private const array SORTABLE = [
        'name' => 'p.name',
        'reference' => 'p.reference',
        'createdAt' => 'p.createdAt',
    ];

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function get(DomainId $id): ?Product
    {
        return $this->find($id);
    }

    public function findByReference(string $reference): ?Product
    {
        return $this->findOneBy(['reference' => $reference]);
    }

    /**
     * @return list<Product>
     */
    public function list(ListProductsQuery $query): array
    {
        $qb = $this->buildListQuery($query);

        if ($query->page && $query->limit) {
            $qb
                ->setFirstResult(($query->page - 1) * $query->limit)
                ->setMaxResults($query->limit)
            ;
        }

        $field = self::SORTABLE[(string) $query->orderField] ?? 'p.createdAt';
        $direction = 'ASC' === strtoupper((string) $query->orderDirection) ? 'ASC' : 'DESC';

        /** @var list<Product> $products */
        $products = $qb->orderBy($field, $direction)->getQuery()->getResult();

        return $products;
    }

    public function totalItems(ListProductsQuery $query): int
    {
        return (int) $this->buildListQuery($query)
            ->select('COUNT(p)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function save(Product $product): void
    {
        $this->getEntityManager()->persist($product);
    }

    public function remove(Product $product): void
    {
        $this->getEntityManager()->remove($product);
    }

    /**
     * Hors de l'interface de dépôt à dessein : elle renvoie un QueryBuilder, qui
     * n'a pas sa place dans Domain.
     */
    private function buildListQuery(ListProductsQuery $query): QueryBuilder
    {
        $qb = $this->createQueryBuilder('p');

        if (null !== $query->status) {
            $qb->andWhere('p.status = :status')->setParameter('status', $query->status->value);
        }

        if (null !== $query->search) {
            $qb
                ->andWhere('LOWER(p.name) LIKE :q OR LOWER(p.reference) LIKE :q')
                ->setParameter('q', '%'.strtolower($query->search).'%')
            ;
        }

        return $qb;
    }
}
