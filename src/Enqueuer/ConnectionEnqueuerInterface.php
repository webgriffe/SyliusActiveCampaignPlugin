<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Enqueuer;

use Sylius\Component\Core\Model\ChannelInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface;

interface ConnectionEnqueuerInterface
{
    /** @param ChannelInterface&ActiveCampaignAwareInterface $channel */
    public function enqueue($channel): void;
}
