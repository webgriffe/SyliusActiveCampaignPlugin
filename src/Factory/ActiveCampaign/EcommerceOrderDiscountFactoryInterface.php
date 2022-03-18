<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign;

use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\EcommerceOrderDiscountInterface;

interface EcommerceOrderDiscountFactoryInterface
{
    public function createNew(): EcommerceOrderDiscountInterface;
}
