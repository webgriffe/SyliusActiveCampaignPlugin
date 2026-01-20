<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\EcommerceOrder;

use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ListResourcesResponseInterface;

/** @psalm-api */
final class ListEcommerceOrdersResponse implements ListResourcesResponseInterface
{
    /** @param EcommerceOrderResponse[] $ecommerceOrders */
    public function __construct(
        private array $ecommerceOrders,
    ) {
    }

    #[\Override]
    public function getResourceResponseLists(): array
    {
        return $this->ecommerceOrders;
    }
}
