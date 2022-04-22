<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign;

use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\EcommerceCustomerInterface;

final class EcommerceCustomerFactory implements EcommerceCustomerFactoryInterface
{
    public function __construct(
        private string $ecommerceCustomerFQCN
    ) {
    }

    public function createNew(string $email, string $connectionId, string $externalId): EcommerceCustomerInterface
    {
        /** @var EcommerceCustomerInterface $ecommerceCustomer */
        $ecommerceCustomer = new $this->ecommerceCustomerFQCN($email, $connectionId, $externalId);

        return $ecommerceCustomer;
    }
}
