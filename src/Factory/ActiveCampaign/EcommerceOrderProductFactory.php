<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign;

use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\EcommerceOrderProduct;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\EcommerceOrderProductInterface;

final class EcommerceOrderProductFactory implements EcommerceOrderProductFactoryInterface
{
    public function createNew(string $name, int $price, int $quantity, string $externalId): EcommerceOrderProductInterface
    {
        return new EcommerceOrderProduct(
            $name,
            $price,
            $quantity,
            $externalId
        );
    }
}
