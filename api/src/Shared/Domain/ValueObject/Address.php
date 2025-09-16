<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

class Address
{
    public function __construct(
        private string $street,
        private ?string $additionalAddress1,
        private ?string $additionalAddress2,
        private string $city,
        private string $zipCode,
        private string $country,
    ) {
    }

    public function getStreet(): string
    {
        return $this->street;
    }

    public function getAdditionalAddress1(): ?string
    {
        return $this->additionalAddress1;
    }

    public function getAdditionalAddress2(): ?string
    {
        return $this->additionalAddress2;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getZipCode(): string
    {
        return $this->zipCode;
    }

    public function getCountry(): string
    {
        return $this->country;
    }
}
