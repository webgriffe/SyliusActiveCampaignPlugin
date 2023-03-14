<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign;

final class ContactList implements ContactListInterface
{
    public function __construct(
        private int $listId,
        private int $contactId,
        private int $status,
        private ?int $sourceId = null,
    ) {
    }

    public function getListId(): int
    {
        return $this->listId;
    }

    public function setListId(int $listId): void
    {
        $this->listId = $listId;
    }

    public function getContactId(): int
    {
        return $this->contactId;
    }

    public function setContactId(int $contactId): void
    {
        $this->contactId = $contactId;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    public function getSourceId(): ?int
    {
        return $this->sourceId;
    }

    public function setSourceId(?int $sourceId): void
    {
        $this->sourceId = $sourceId;
    }
}
