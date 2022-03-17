<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign;

use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\EcommerceCustomerInterface;

interface EcommerceCustomerFactoryInterface
{
    public function createNew(string $email, string $connectionId, string $externalId): EcommerceCustomerInterface;
}
