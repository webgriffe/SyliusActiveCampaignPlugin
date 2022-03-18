<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\EcommerceOrder;

use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\CreateResourceResponseInterface;

final class CreateEcommerceOrderResponse implements CreateResourceResponseInterface
{
    public function __construct(
        private CreateEcommerceOrderEcommerceOrderResponse $order
    ) {
    }

    public function getOrder(): CreateEcommerceOrderEcommerceOrderResponse
    {
        return $this->order;
    }
}
