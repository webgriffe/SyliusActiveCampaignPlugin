<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Enqueuer;

use Sylius\Component\Core\Model\OrderInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface;

interface EcommerceOrderEnqueuerInterface
{
    /** @param OrderInterface&ActiveCampaignAwareInterface $order */
    public function enqueue($order, bool $isInRealTime = true): void;
}
