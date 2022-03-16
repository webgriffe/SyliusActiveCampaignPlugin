<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response;

final class ContactResponse
{
    /** @param array<string, string> $links */
    public function __construct(
        private string $email,
        private string $createdAt,
        private string $updatedAt,
        private string $organizationId,
        private array $links,
        private int $id,
        private string $organization
    ) {
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }

    public function getOrganizationId(): string
    {
        return $this->organizationId;
    }

    public function getLinks(): array
    {
        return $this->links;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getOrganization(): string
    {
        return $this->organization;
    }
}
