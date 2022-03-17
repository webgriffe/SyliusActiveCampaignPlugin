<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign;

interface ContactInterface extends ResourceInterface
{
    public function getEmail(): string;

    public function setEmail(string $email): void;

    public function getFirstName(): ?string;

    public function setFirstName(?string $firstName): void;

    public function getLastName(): ?string;

    public function setLastName(?string $lastName): void;

    /** @return FieldValueInterface[] */
    public function getFieldValues(): array;

    /** @param FieldValueInterface[] $fieldValues */
    public function setFieldValues(array $fieldValues): void;

    public function getPhone(): ?string;

    public function setPhone(?string $phone): void;
}
