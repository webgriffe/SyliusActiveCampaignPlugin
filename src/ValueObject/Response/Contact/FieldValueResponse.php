<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Contact;

final class FieldValueResponse
{
    /** @param array<string, string> $links */
    public function __construct(
        private string $contact,
        private string $field,
        private string $value,
        private string $createdAt,
        private string $updatedAt,
        private array $links,
        private string $id,
        private string $owner
    ) {
    }

    public function getContact(): string
    {
        return $this->contact;
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }

    public function getLinks(): array
    {
        return $this->links;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getOwner(): string
    {
        return $this->owner;
    }
}
