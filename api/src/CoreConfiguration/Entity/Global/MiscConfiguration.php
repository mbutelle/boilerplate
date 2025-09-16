<?php

declare(strict_types=1);

namespace App\CoreConfiguration\Entity\Global;

use ApiPlatform\Metadata\ApiResource;
use App\CoreConfiguration\Repository\Global\MiscConfigurationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MiscConfigurationRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['read:misc_configuration']],
    denormalizationContext: ['groups' => ['write:misc_configuration']],
    security: 'is_granted("ROLE_ADMIN")'
)]
class MiscConfiguration
{
    public function __construct(
        #[ORM\Id]
        #[ORM\GeneratedValue(strategy: 'AUTO')]
        #[ORM\Column]
        #[Groups(['read:misc_configuration'])]
        public ?int $id = null,

        #[ORM\Column(type: 'string', length: 255, unique: true)]
        #[Assert\NotBlank]
        #[Groups(['read:misc_configuration', 'write:misc_configuration'])]
        private string $key = '',

        #[ORM\Column(type: 'string', length: 255)]
        #[Assert\NotBlank]
        #[Groups(['read:misc_configuration', 'write:misc_configuration'])]
        private string $value = '',

        #[ORM\Column(type: 'string', length: 255, nullable: true)]
        #[Groups(['read:misc_configuration', 'write:misc_configuration'])]
        private ?string $description = null,
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function setKey(string $key): self
    {
        $this->key = $key;

        return $this;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
