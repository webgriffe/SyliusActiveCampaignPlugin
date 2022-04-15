<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Resolver;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ChannelActiveCampaignAwareInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\CustomerActiveCampaignAwareInterface;

interface ListSubscriptionStatusResolverInterface
{
    public const UNCONFIRMED_STATUS_CODE = 0;

    public const SUBSCRIBED_STATUS_CODE = 1;

    public const UNSUBSCRIBED_STATUS_CODE = 2;

    public const BOUNCED_STATUS_CODE = 3;

    /**
     * @param CustomerInterface&CustomerActiveCampaignAwareInterface $customer
     * @param ChannelInterface&ChannelActiveCampaignAwareInterface $channel
     */
    public function resolve($customer, $channel): int;
}
