<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Message\Connection;

final class ConnectionUpdate
{
    /** @param string|int $channelId */
    public function __construct(
        private mixed $channelId,
        private int $activeCampaignId,
    ) {
    }

    /** @return string|int */
    public function getChannelId(): mixed
    {
        return $this->channelId;
    }

    public function getActiveCampaignId(): int
    {
        return $this->activeCampaignId;
    }
}
