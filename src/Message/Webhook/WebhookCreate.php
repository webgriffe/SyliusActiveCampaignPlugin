<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Message\Webhook;

final class WebhookCreate
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
