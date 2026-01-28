<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign;

/** @psalm-api */
final class Contact implements ContactInterface
{
    /** @param FieldValueInterface[] $fieldValues */
    public function __construct(
        private string $email,
        private ?string $firstName = null,
        private ?string $lastName = null,
        private ?string $phone = null,
        private array $fieldValues = [],
    ) {
    }

    #[\Override]
    public function getEmail(): string
    {
        return $this->email;
    }

    #[\Override]
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    #[\Override]
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    #[\Override]
    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
    }

    #[\Override]
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    #[\Override]
    public function setLastName(?string $lastName): void
    {
        $this->lastName = $lastName;
    }

    #[\Override]
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    #[\Override]
    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }

    #[\Override]
    public function getFieldValues(): array
    {
        return $this->fieldValues;
    }

    #[\Override]
    public function setFieldValues(array $fieldValues): void
    {
        $this->fieldValues = $fieldValues;
    }
}
