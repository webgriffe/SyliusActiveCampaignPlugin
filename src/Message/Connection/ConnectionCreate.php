<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Message\Connection;

final class ConnectionCreate
{
    /** @param string|int $channelId */
    public function __construct(
        private mixed $channelId
    ) {
    }

    /** @return string|int */
    public function getChannelId(): mixed
    {
        return $this->channelId;
    }
}
