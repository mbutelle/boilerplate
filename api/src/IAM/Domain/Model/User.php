<?php

declare(strict_types=1);

namespace App\IAM\Domain\Model;

use App\Shared\Domain\Contracts\DomainId;
use App\Shared\Domain\Traits\Timestampable;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Modèle de domaine : aucun attribut Doctrine ni API Platform ici. Le mapping
 * vit dans config/doctrine/mapping/IAM/, l'exposition dans Infrastructure/ApiPlatform/.
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use Timestampable;

    public const string ROLE_USER = 'ROLE_USER';
    public const string ROLE_ADMIN = 'ROLE_ADMIN';
    public const string ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    private ?string $password = null;
    private ?string $resetToken = null;
    private ?\DateTimeImmutable $resetAt = null;
    private ?\DateTimeImmutable $resetRequestedAt = null;

    /**
     * @param list<string> $roles
     */
    public function __construct(
        private DomainId $id,
        private string $email,
        private array $roles = [self::ROLE_USER],
    ) {
        $this->createdAt = new \DateTime();
    }

    public static function createAdmin(string $email): self
    {
        return new self(new DomainId(), $email, [self::ROLE_ADMIN]);
    }

    public static function create(string $email): self
    {
        return new self(new DomainId(), $email);
    }

    public function getId(): DomainId
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    /**
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = self::ROLE_USER;

        return array_values(array_unique($roles));
    }

    public function isAdmin(): bool
    {
        return in_array(self::ROLE_ADMIN, $this->roles, true)
            || in_array(self::ROLE_SUPER_ADMIN, $this->roles, true);
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $hashedPassword): void
    {
        $this->password = $hashedPassword;
    }

    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    public function getResetAt(): ?\DateTimeImmutable
    {
        return $this->resetAt;
    }

    public function getResetRequestedAt(): ?\DateTimeImmutable
    {
        return $this->resetRequestedAt;
    }

    public function requestReset(): void
    {
        $this->resetToken = bin2hex(random_bytes(32));
        $this->resetRequestedAt = new \DateTimeImmutable();
    }

    public function resetPassword(string $hashedPassword): void
    {
        $this->password = $hashedPassword;
        $this->resetToken = null;
        $this->resetRequestedAt = null;
        $this->resetAt = new \DateTimeImmutable();
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
    }
}
