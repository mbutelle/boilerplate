<?php

declare(strict_types=1);

namespace App\Security\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use App\Security\Repository\UserRepository;
use App\Security\State\Processor\UpdatePasswordProcessor;
use App\Security\State\Provider\AdminsProvider;
use App\Security\State\Provider\MeProvider;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/users/me',
            security: 'object == user',
            provider: MeProvider::class,
        ),
        new Patch(
            uriTemplate: '/users/{id}/update_password',
            denormalizationContext: ['groups' => ['reset_password:write']],
            processor: UpdatePasswordProcessor::class,
        ),
        new Delete(
            security: "is_granted('ROLE_ADMIN')",
        ),
        new GetCollection(
            security: "is_granted('ROLE_ADMIN')",
            provider: AdminsProvider::class,
        ),
    ],
)]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table('`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    public const string ROLE_USER = 'ROLE_USER';
    public const string ROLE_ADMIN = 'ROLE_ADMIN';
    public const string ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';
    public const string ROLE_ANONYMOUS = 'ROLE_ANONYMOUS';

    public function __construct(
        #[ORM\Id]
        #[ORM\GeneratedValue]
        #[ORM\Column]
        public ?int $id = null,
        #[ORM\Column(length: 180)]
        public ?string $username = null,
        /**
         * @var list<string> The user roles
         */
        #[ORM\Column]
        public array $roles = [],
        #[ORM\Column]
        #[ApiProperty(readable: false)]
        #[Groups('reset_password:write')]
        public ?string $password = null,
        #[ORM\Column(length: 255)]
        #[Groups('password-reset-requests:write')]
        public ?string $email = null,
        #[ORM\Column(length: 255, nullable: true)]
        #[ApiProperty(readable: false, writable: false)]
        public ?string $resetToken = null,
        #[ORM\Column(nullable: true)]
        public ?\DateTimeImmutable $resetAt = null,
        #[ORM\Column(nullable: true)]
        public ?\DateTimeImmutable $resetRequestedAt = null,
    ) {
    }

    public static function createAdmin(string $email): self
    {
        return new self(
            null,
            $email,
            [self::ROLE_ADMIN],
            null,
            $email,
        );
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = self::ROLE_USER;

        return $roles;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function requestReset(): void
    {
        $this->resetToken = bin2hex(random_bytes(32));
        $this->resetRequestedAt = new \DateTimeImmutable();
    }

    public function resetPassword(): void
    {
        $this->resetToken = null;
        $this->resetRequestedAt = null;
        $this->resetAt = new \DateTimeImmutable();
    }

    public function isAdmin(): bool
    {
        return in_array(self::ROLE_ADMIN, $this->roles, true);
    }
}
