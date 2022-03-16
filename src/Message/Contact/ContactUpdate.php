<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Message\Contact;

final class ContactUpdate
{
    /** @param string|int $customerId */
    public function __construct(
        private mixed $customerId,
        private int $activeCampaignId
    ) {
    }

    /** @return string|int */
    public function getCustomerId(): mixed
    {
        return $this->customerId;
    }

    public function getActiveCampaignId(): int
    {
        return $this->activeCampaignId;
    }
}
