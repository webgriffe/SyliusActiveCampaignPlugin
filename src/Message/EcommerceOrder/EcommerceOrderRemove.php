<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceOrder;

final class EcommerceOrderRemove
{
    public function __construct(
        private int $activeCampaignId
    ) {
    }

    public function getActiveCampaignId(): int
    {
        return $this->activeCampaignId;
    }
}
