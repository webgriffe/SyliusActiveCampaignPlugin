<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign;

/** @psalm-api */
final class ContactList implements ContactListInterface
{
    public function __construct(
        private int $listId,
        private int $contactId,
        private int $status,
        private ?int $sourceId = null,
    ) {
    }

    #[\Override]
    public function getListId(): int
    {
        return $this->listId;
    }

    #[\Override]
    public function setListId(int $listId): void
    {
        $this->listId = $listId;
    }

    #[\Override]
    public function getContactId(): int
    {
        return $this->contactId;
    }

    #[\Override]
    public function setContactId(int $contactId): void
    {
        $this->contactId = $contactId;
    }

    #[\Override]
    public function getStatus(): int
    {
        return $this->status;
    }

    #[\Override]
    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    #[\Override]
    public function getSourceId(): ?int
    {
        return $this->sourceId;
    }

    #[\Override]
    public function setSourceId(?int $sourceId): void
    {
        $this->sourceId = $sourceId;
    }
}
