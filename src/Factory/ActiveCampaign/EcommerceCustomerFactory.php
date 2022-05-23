<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign;

use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\EcommerceCustomerInterface;

final class EcommerceCustomerFactory extends AbstractFactory implements EcommerceCustomerFactoryInterface
{
    public function createNew(string $email, string $connectionId, string $externalId): EcommerceCustomerInterface
    {
        /** @var EcommerceCustomerInterface $ecommerceCustomer */
        $ecommerceCustomer = new $this->targetClassFQCN($email, $connectionId, $externalId);

        return $ecommerceCustomer;
    }
}
