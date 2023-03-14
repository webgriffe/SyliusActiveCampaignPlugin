<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Message\Connection;

final class ConnectionRemove
{
    public function __construct(
        private int $activeCampaignId,
    ) {
    }

    public function getActiveCampaignId(): int
    {
        return $this->activeCampaignId;
    }
}
