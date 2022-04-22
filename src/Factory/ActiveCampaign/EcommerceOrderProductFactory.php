<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign;

use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\EcommerceOrderProductInterface;

final class EcommerceOrderProductFactory implements EcommerceOrderProductFactoryInterface
{
    public function __construct(
        private string $ecommerceOrderFQCN
    ) {
    }

    public function createNew(string $name, int $price, int $quantity, string $externalId): EcommerceOrderProductInterface
    {
        /** @var EcommerceOrderProductInterface $ecommerceOrderProduct */
        $ecommerceOrderProduct = new $this->ecommerceOrderFQCN(
            $name,
            $price,
            $quantity,
            $externalId
        );

        return $ecommerceOrderProduct;
    }
}
