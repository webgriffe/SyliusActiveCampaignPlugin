<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign;

use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\EcommerceOrderDiscountInterface;

final class EcommerceOrderDiscountFactory implements EcommerceOrderDiscountFactoryInterface
{
    public function __construct(
        private string $ecommerceOrderDiscountFQCN
    ) {
    }

    public function createNew(): EcommerceOrderDiscountInterface
    {
        /** @var EcommerceOrderDiscountInterface $ecommerceOrderDiscount */
        $ecommerceOrderDiscount = new $this->ecommerceOrderDiscountFQCN();

        return $ecommerceOrderDiscount;
    }
}
