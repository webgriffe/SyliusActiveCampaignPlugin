<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\EcommerceOrder;

use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\CreateResourceResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ResourceResponseInterface;

final class CreateEcommerceOrderResponse implements CreateResourceResponseInterface
{
    public function __construct(
        private EcommerceOrderResponse $ecommerceOrder
    ) {
    }

    public function getResourceResponse(): ResourceResponseInterface
    {
        return $this->ecommerceOrder;
    }
}
