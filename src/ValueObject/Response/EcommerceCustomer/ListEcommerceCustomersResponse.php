<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\EcommerceCustomer;

use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ListResourcesResponseInterface;

final class ListEcommerceCustomersResponse implements ListResourcesResponseInterface
{
    /**
     * @param EcommerceCustomerResponse[] $ecommerceCustomers
     */
    public function __construct(
        private array $ecommerceCustomers
    ) {
    }

    /** @return EcommerceCustomerResponse[] */
    public function getResourceResponseLists(): array
    {
        return $this->ecommerceCustomers;
    }
}
