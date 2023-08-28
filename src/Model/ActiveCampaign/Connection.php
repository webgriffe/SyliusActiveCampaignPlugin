<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign;

final class Connection implements ConnectionInterface
{
    private const SYLIUS_LOGO_URL = 'https://raw.githubusercontent.com/webgriffe/SyliusActiveCampaignPlugin/master/docs/images/sylius-logo.png';

    private const WEBGRIFFE_SYLIUS_ACTIVE_CAMPAIGN_PLUGIN_GITHUB_URL = 'https://webgriffe.github.io/SyliusActiveCampaignPlugin';

    public function __construct(
        private string $service,
        private string $externalId,
        private string $name,
        private string $logoUrl = self::SYLIUS_LOGO_URL,
        private string $linkUrl = self::WEBGRIFFE_SYLIUS_ACTIVE_CAMPAIGN_PLUGIN_GITHUB_URL,
    ) {
    }

    public function getService(): string
    {
        return $this->service;
    }

    public function setService(string $service): void
    {
        $this->service = $service;
    }

    public function getExternalId(): string
    {
        return $this->externalId;
    }

    public function setExternalId(string $externalId): void
    {
        $this->externalId = $externalId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getLogoUrl(): string
    {
        return $this->logoUrl;
    }

    public function setLogoUrl(string $logoUrl): void
    {
        $this->logoUrl = $logoUrl;
    }

    public function getLinkUrl(): string
    {
        return $this->linkUrl;
    }

    public function setLinkUrl(string $linkUrl): void
    {
        $this->linkUrl = $linkUrl;
    }
}
