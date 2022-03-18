<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceCustomer;

final class EcommerceCustomerCreate
{
    /**
     * @param string|int $customerId
     * @param string|int $channelId
     */
    public function __construct(
        private mixed $customerId,
        private mixed $channelId
    ) {
    }

    /** @return string|int */
    public function getCustomerId(): mixed
    {
        return $this->customerId;
    }

    /** @return string|int */
    public function getChannelId(): mixed
    {
        return $this->channelId;
    }
}
