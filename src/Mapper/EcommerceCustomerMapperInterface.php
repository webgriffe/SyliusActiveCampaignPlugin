<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Mapper;

use Sylius\Component\Core\Model\CustomerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\EcommerceCustomerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface;

interface EcommerceCustomerMapperInterface
{
    public function mapFromCustomerAndChannel(CustomerInterface $customer, ActiveCampaignAwareInterface $channel): EcommerceCustomerInterface;
}
