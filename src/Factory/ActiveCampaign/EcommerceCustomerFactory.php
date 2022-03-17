<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign;

use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\EcommerceCustomer;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\EcommerceCustomerInterface;

final class EcommerceCustomerFactory implements EcommerceCustomerFactoryInterface
{
    public function createNew(string $email, string $connectionId, string $externalId): EcommerceCustomerInterface
    {
        return new EcommerceCustomer($email, $connectionId, $externalId);
    }
}
