<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Enqueuer;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\CustomerActiveCampaignAwareInterface;

interface EcommerceCustomerEnqueuerInterface
{
    /**
     * @param CustomerInterface&CustomerActiveCampaignAwareInterface $customer
     * @param ChannelInterface&ActiveCampaignAwareInterface $channel
     */
    public function enqueue($customer, $channel): void;
}
