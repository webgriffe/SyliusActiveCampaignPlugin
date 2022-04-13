<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign;

final class ContactTag implements ContactTagInterface
{
    public function __construct(
        private int $contactId,
        private int $tagId
    ) {
    }

    public function getContactId(): int
    {
        return $this->contactId;
    }

    public function setContactId(int $contactId): void
    {
        $this->contactId = $contactId;
    }

    public function getTagId(): int
    {
        return $this->tagId;
    }

    public function setTagId(int $tagId): void
    {
        $this->tagId = $tagId;
    }
}
