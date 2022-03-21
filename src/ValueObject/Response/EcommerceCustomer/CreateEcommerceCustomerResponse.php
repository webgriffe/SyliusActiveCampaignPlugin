<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\EcommerceCustomer;

use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\CreateResourceResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ResourceResponseInterface;

final class CreateEcommerceCustomerResponse implements CreateResourceResponseInterface
{
    public function __construct(
        private EcommerceCustomerResponse $ecomCustomerResponse
    ) {
    }

    public function getResourceResponse(): ResourceResponseInterface
    {
        return $this->ecomCustomerResponse;
    }
}
