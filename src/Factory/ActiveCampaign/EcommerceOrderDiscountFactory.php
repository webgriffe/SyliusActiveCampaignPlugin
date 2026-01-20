<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign;

use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\EcommerceOrderDiscountInterface;

final class EcommerceOrderDiscountFactory extends AbstractFactory implements EcommerceOrderDiscountFactoryInterface
{
    #[\Override]
    public function createNew(): EcommerceOrderDiscountInterface
    {
        /** @var class-string<EcommerceOrderDiscountInterface> $class */
        $class = $this->targetClassFQCN;

        return new $class();
    }
}
