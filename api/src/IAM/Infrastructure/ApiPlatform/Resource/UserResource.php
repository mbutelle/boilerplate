<?php

declare(strict_types=1);

namespace App\IAM\Infrastructure\ApiPlatform\Resource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\IAM\Application\Command\DeleteUser\DeleteUserCommand;
use App\IAM\Domain\Model\User;
use App\IAM\Infrastructure\ApiPlatform\Payload\ChangePasswordPayload;
use App\IAM\Infrastructure\ApiPlatform\Payload\CreateAdminPayload;
use App\IAM\Infrastructure\ApiPlatform\State\Provider\AdminCollectionProvider;
use App\IAM\Infrastructure\ApiPlatform\State\Provider\MeProvider;
use App\IAM\Infrastructure\ApiPlatform\State\Provider\UserItemProvider;
use App\Shared\Application\Command\CommandInterface;
use App\Shared\Infrastructure\ApiPlatform\Resource\ResourceInterface;
use App\Shared\Infrastructure\ApiPlatform\State\Processor\ApplyCommandProcessor;

#[ApiResource(
    shortName: 'User',
    operations: [
        new Get(
            uriTemplate: '/users/me',
            provider: MeProvider::class,
        ),
        new GetCollection(
            uriTemplate: '/users',
            security: "is_granted('ROLE_ADMIN')",
            provider: AdminCollectionProvider::class,
        ),
        new Post(
            uriTemplate: '/users/admin',
            security: "is_granted('ROLE_ADMIN')",
            input: CreateAdminPayload::class,
            processor: ApplyCommandProcessor::class,
        ),
        new Patch(
            uriTemplate: '/users/{id}/update_password',
            security: "is_granted('ROLE_ADMIN') or object.id == user.getId().toRfc4122()",
            input: ChangePasswordPayload::class,
            provider: UserItemProvider::class,
            processor: ApplyCommandProcessor::class,
        ),
        new Delete(
            uriTemplate: '/users/{id}',
            security: "is_granted('ROLE_ADMIN')",
            provider: UserItemProvider::class,
            processor: ApplyCommandProcessor::class,
        ),
    ],
)]
final class UserResource implements ResourceInterface
{
    /**
     * @param list<string> $roles
     */
    public function __construct(
        #[ApiProperty(identifier: true)]
        public ?string $id = null,
        public ?string $email = null,
        public array $roles = [],
        public ?\DateTimeImmutable $createdAt = null,
    ) {
    }

    /**
     * @param array<string, mixed> $context
     */
    public static function fromDomain(object $domain, array $context = []): self
    {
        if (!$domain instanceof User) {
            throw new \InvalidArgumentException(sprintf('Expected %s, got %s.', User::class, get_debug_type($domain)));
        }

        $createdAt = $domain->getCreatedAt();

        return new self(
            (string) $domain->getId(),
            $domain->getEmail(),
            $domain->getRoles(),
            $createdAt instanceof \DateTimeInterface
                ? \DateTimeImmutable::createFromInterface($createdAt)
                : null,
        );
    }

    /**
     * Masqué explicitement : sans cela le sérialiseur voit un getter et publie
     * une propriété "deleteCommand" dans le schéma, donc dans le SDK des fronts.
     * Le piège vaut pour toute ressource implémentant ResourceInterface.
     */
    #[ApiProperty(readable: false, writable: false)]
    public function getDeleteCommand(): ?CommandInterface
    {
        return new DeleteUserCommand((string) $this->id);
    }
}
