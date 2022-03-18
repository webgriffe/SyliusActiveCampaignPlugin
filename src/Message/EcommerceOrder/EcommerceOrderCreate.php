<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceOrder;

final class EcommerceOrderCreate
{
    /** @param string|int $orderId */
    public function __construct(
        private mixed $orderId,
        private bool $isInRealTime = true
    ) {
    }

    /** @return string|int */
    public function getOrderId(): mixed
    {
        return $this->orderId;
    }

    public function isInRealTime(): bool
    {
        return $this->isInRealTime;
    }
}
