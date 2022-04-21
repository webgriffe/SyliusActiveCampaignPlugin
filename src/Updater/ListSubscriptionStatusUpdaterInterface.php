<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Updater;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;

interface ListSubscriptionStatusUpdaterInterface
{
    public function update(CustomerInterface $customer, ChannelInterface $channel, int $listSubscriptionStatus): void;
}
