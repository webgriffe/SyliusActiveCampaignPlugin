<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Mapper;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\EcommerceCustomerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface;

interface EcommerceCustomerMapperInterface
{
    /** @param ChannelInterface&ActiveCampaignAwareInterface $channel */
    public function mapFromCustomerAndChannel(CustomerInterface $customer, $channel): EcommerceCustomerInterface;
}
