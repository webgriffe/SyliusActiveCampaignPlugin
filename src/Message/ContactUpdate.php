<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Message;

final class ContactUpdate
{
    /** @param string|int $customerId */
    public function __construct(
        private mixed $customerId,
        private string $activeCampaignId
    ) {
    }

    /** @return string|int */
    public function getCustomerId(): mixed
    {
        return $this->customerId;
    }

    public function getActiveCampaignId(): string
    {
        return $this->activeCampaignId;
    }
}
