<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Model;

interface ActiveCampaignContactInterface
{
    public function getEmail(): string;

    public function setEmail(string $email): void;

    public function getFirstName(): ?string;

    public function setFirstName(?string $firstName): void;

    public function getLastName(): ?string;

    public function setLastName(?string $lastName): void;

    public function getFieldValues(): array;

    public function setFieldValues(array $fieldValues): void;

    public function getPhone(): ?int;

    public function setPhone(?int $phone): void;
}
