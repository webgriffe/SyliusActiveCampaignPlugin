<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceCustomer;

final class EcommerceCustomerUpdate
{
    /**
     * @param string|int $customerId
     * @param string|int $channelId
     */
    public function __construct(
        private mixed $customerId,
        private int $activeCampaignId,
        private mixed $channelId,
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

    /** @return string|int */
    public function getChannelId(): mixed
    {
        return $this->channelId;
    }
}
