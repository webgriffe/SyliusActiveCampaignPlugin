<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\EcommerceOrder;

use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ResourceResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\UpdateResourceResponseInterface;

/** @psalm-api */
final class UpdateEcommerceOrderResponse implements UpdateResourceResponseInterface
{
    public function __construct(
        private EcommerceOrderResponse $ecommerceOrder,
    ) {
    }

    #[\Override]
    public function getResourceResponse(): ResourceResponseInterface
    {
        return $this->ecommerceOrder;
    }
}
