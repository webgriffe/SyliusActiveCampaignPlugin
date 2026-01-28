<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign;

/** @psalm-api */
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

    #[\Override]
    public function getService(): string
    {
        return $this->service;
    }

    #[\Override]
    public function setService(string $service): void
    {
        $this->service = $service;
    }

    #[\Override]
    public function getExternalId(): string
    {
        return $this->externalId;
    }

    #[\Override]
    public function setExternalId(string $externalId): void
    {
        $this->externalId = $externalId;
    }

    #[\Override]
    public function getName(): string
    {
        return $this->name;
    }

    #[\Override]
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    #[\Override]
    public function getLogoUrl(): string
    {
        return $this->logoUrl;
    }

    #[\Override]
    public function setLogoUrl(string $logoUrl): void
    {
        $this->logoUrl = $logoUrl;
    }

    #[\Override]
    public function getLinkUrl(): string
    {
        return $this->linkUrl;
    }

    #[\Override]
    public function setLinkUrl(string $linkUrl): void
    {
        $this->linkUrl = $linkUrl;
    }
}
