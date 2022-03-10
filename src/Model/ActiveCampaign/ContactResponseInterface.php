<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign;

interface ContactResponseInterface
{
    /** @return FieldValueInterface[] */
    public function getFieldValues(): array;

    public function getEmail(): string;

    public function getCreatedAt(): string;

    public function getUpdatedAt(): string;

    public function getOriginalId(): string;

    public function getLinks(): array;

    public function getId(): string;

    public function getOrganization(): string;
}
