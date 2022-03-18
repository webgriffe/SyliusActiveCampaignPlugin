<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\EcommerceCustomer;

use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\CreateResourceResponseInterface;

final class CreateEcommerceCustomerResponse implements CreateResourceResponseInterface
{
    public function __construct(
        private CreateEcommerceCustomerEcomCustomerResponse $ecomCustomerResponse
    ) {
    }

    public function getEcomCustomer(): CreateEcommerceCustomerEcomCustomerResponse
    {
        return $this->ecomCustomerResponse;
    }
}
