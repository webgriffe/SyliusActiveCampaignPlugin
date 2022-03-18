<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign;

use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\EcommerceOrderProductInterface;

interface EcommerceOrderProductFactoryInterface
{
    public function createNew(string $name, int $price, int $quantity, string $externalId): EcommerceOrderProductInterface;
}
