<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign;

use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\EcommerceOrderDiscountInterface;

final class EcommerceOrderDiscountFactory extends AbstractFactory implements EcommerceOrderDiscountFactoryInterface
{
    public function createNew(): EcommerceOrderDiscountInterface
    {
        /** @var EcommerceOrderDiscountInterface $ecommerceOrderDiscount */
        $ecommerceOrderDiscount = new $this->targetClassFQCN();

        return $ecommerceOrderDiscount;
    }
}
