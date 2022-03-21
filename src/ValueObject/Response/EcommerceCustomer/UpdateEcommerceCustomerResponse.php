<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\EcommerceCustomer;

use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ResourceResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\UpdateResourceResponseInterface;

final class UpdateEcommerceCustomerResponse implements UpdateResourceResponseInterface
{
    public function __construct(
        private EcommerceCustomerResponse $customer
    ) {
    }

    public function getResourceResponse(): ResourceResponseInterface
    {
        return $this->customer;
    }
}
