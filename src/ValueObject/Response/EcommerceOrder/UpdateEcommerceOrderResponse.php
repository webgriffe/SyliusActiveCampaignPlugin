<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\EcommerceOrder;

use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ResourceResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\UpdateResourceResponseInterface;

final class UpdateEcommerceOrderResponse implements UpdateResourceResponseInterface
{
    public function __construct(
        private EcommerceOrderResponse $order
    ) {
    }

    public function getResourceResponse(): ResourceResponseInterface
    {
        return $this->order;
    }
}
