<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceOrder;

final class EcommerceOrderUpdate
{
    /** @param string|int $orderId */
    public function __construct(
        private mixed $orderId,
        private int $activeCampaignId,
        private bool $isInRealTime = true
    ) {
    }

    /** @return string|int */
    public function getOrderId(): mixed
    {
        return $this->orderId;
    }

    public function getActiveCampaignId(): int
    {
        return $this->activeCampaignId;
    }

    public function isInRealTime(): bool
    {
        return $this->isInRealTime;
    }
}
