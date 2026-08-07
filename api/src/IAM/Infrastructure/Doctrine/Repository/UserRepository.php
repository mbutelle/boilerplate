<?php

declare(strict_types=1);

namespace App\IAM\Infrastructure\Doctrine\Repository;

use App\IAM\Domain\Contract\Repository\UserRepositoryInterface;
use App\IAM\Domain\Model\User;
use App\Shared\Domain\Contracts\DomainId;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements UserRepositoryInterface, PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function get(DomainId $id): ?User
    {
        return $this->find($id);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->findOneBy(['email' => $email]);
    }

    public function findByResetToken(string $token): ?User
    {
        return $this->findOneBy(['resetToken' => $token]);
    }

    /**
     * Le filtrage se fait en SQL : charger tous les utilisateurs pour les trier
     * en PHP ne tient pas au-delà du jeu de données de démonstration.
     *
     * Le LIKE plutôt que JSON_CONTAINS est délibéré : cette dernière est une
     * fonction MySQL que DQL ne connaît pas sans déclarer une extension. Les
     * rôles étant sérialisés entre guillemets dans le tableau JSON, chercher
     * "ROLE_ADMIN" guillemets compris ne peut pas heurter un préfixe voisin.
     *
     * @return list<User>
     */
    public function findAdmins(): array
    {
        /** @var list<User> $admins */
        $admins = $this->createQueryBuilder('u')
            ->andWhere('u.roles LIKE :admin OR u.roles LIKE :superAdmin')
            ->setParameter('admin', '%"'.User::ROLE_ADMIN.'"%')
            ->setParameter('superAdmin', '%"'.User::ROLE_SUPER_ADMIN.'"%')
            ->orderBy('u.email', 'ASC')
            ->getQuery()
            ->getResult();

        return $admins;
    }

    /**
     * save() persiste sans flusher : c'est le middleware doctrine_transaction du
     * bus de commande qui commite en fin de handler.
     */
    public function save(User $user): void
    {
        $this->getEntityManager()->persist($user);
    }

    public function remove(User $user): void
    {
        $this->getEntityManager()->remove($user);
    }

    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->flush();
    }
}
