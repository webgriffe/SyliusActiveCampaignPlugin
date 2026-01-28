<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign;

/** @psalm-api */
final class ContactTag implements ContactTagInterface
{
    public function __construct(
        private int $contactId,
        private int $tagId,
    ) {
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
    public function getTagId(): int
    {
        return $this->tagId;
    }

    #[\Override]
    public function setTagId(int $tagId): void
    {
        $this->tagId = $tagId;
    }
}
