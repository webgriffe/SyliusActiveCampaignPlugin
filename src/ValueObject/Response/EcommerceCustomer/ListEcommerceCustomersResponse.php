<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\EcommerceCustomer;

use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ListResourcesResponseInterface;

/** @psalm-api */
final class ListEcommerceCustomersResponse implements ListResourcesResponseInterface
{
    /**
     * @param EcommerceCustomerResponse[] $ecommerceCustomers
     */
    public function __construct(
        private array $ecommerceCustomers,
    ) {
    }

    /** @return EcommerceCustomerResponse[] */
    #[\Override]
    public function getResourceResponseLists(): array
    {
        return $this->ecommerceCustomers;
    }
}
