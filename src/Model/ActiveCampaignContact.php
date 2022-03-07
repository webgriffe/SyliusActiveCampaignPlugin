<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Model;

final class ActiveCampaignContact implements ActiveCampaignContactInterface
{
    public function __construct(
        private string $email,
        private ?string $firstName = null,
        private ?string $lastName = null,
        private ?int $phone = null,
        private array $fieldValues = []
    ) {
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getPhone(): ?int
    {
        return $this->phone;
    }

    public function setPhone(?int $phone): void
    {
        $this->phone = $phone;
    }

    public function getFieldValues(): array
    {
        return $this->fieldValues;
    }

    public function setFieldValues(array $fieldValues): void
    {
        $this->fieldValues = $fieldValues;
    }
}
