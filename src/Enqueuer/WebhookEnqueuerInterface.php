<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Enqueuer;

use Sylius\Component\Core\Model\ChannelInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ChannelActiveCampaignAwareInterface;

interface WebhookEnqueuerInterface
{
    /** @param ChannelInterface&ChannelActiveCampaignAwareInterface $channel */
    public function enqueue($channel): void;
}
