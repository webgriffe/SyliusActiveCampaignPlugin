<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Connection;

use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ResourceResponseInterface;

final class UpdateConnectionConnectionResponse implements ResourceResponseInterface
{
    /** @param array<string, string> $links */
    public function __construct(
        private string $service,
        private string $externalId,
        private string $name,
        private int $isInternal,
        private string $status,
        private string $syncStatus,
        private string $logoUrl,
        private string $linkUrl,
        private string $createdAt,
        private string $updatedAt,
        private array $links,
        private int $id
    ) {
    }

    public function getService(): string
    {
        return $this->service;
    }

    public function getExternalId(): string
    {
        return $this->externalId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getIsInternal(): int
    {
        return $this->isInternal;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getSyncStatus(): string
    {
        return $this->syncStatus;
    }

    public function getLogoUrl(): string
    {
        return $this->logoUrl;
    }

    public function getLinkUrl(): string
    {
        return $this->linkUrl;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }

    /** @return array<string, string> */
    public function getLinks(): array
    {
        return $this->links;
    }

    public function getId(): int
    {
        return $this->id;
    }
}
