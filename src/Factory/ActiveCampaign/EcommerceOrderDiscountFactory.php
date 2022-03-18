<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign;

use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\EcommerceOrderDiscount;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\EcommerceOrderDiscountInterface;

final class EcommerceOrderDiscountFactory implements EcommerceOrderDiscountFactoryInterface
{
    public function createNew(): EcommerceOrderDiscountInterface
    {
        return new EcommerceOrderDiscount();
    }
}
