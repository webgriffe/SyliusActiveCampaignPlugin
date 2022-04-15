<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Resolver;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ChannelActiveCampaignAwareInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\CustomerActiveCampaignAwareInterface;

interface ListSubscriptionStatusResolverInterface
{
    /**
     * @param CustomerInterface&CustomerActiveCampaignAwareInterface $customer
     * @param ChannelInterface&ChannelActiveCampaignAwareInterface $channel
     */
    public function resolve($customer, $channel): bool;
}
