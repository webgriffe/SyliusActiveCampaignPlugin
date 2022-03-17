<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign;

interface ConnectionInterface extends ResourceInterface
{
    public function getService(): string;

    public function setService(string $service): void;

    public function getExternalId(): string;

    public function setExternalId(string $externalId): void;

    public function getName(): string;

    public function setName(string $name): void;

    public function getLogoUrl(): string;

    public function setLogoUrl(string $logoUrl): void;

    public function getLinkUrl(): string;

    public function setLinkUrl(string $linkUrl): void;
}
