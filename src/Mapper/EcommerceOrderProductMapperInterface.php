<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Mapper;

use Sylius\Component\Core\Model\OrderItemInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\EcommerceOrderProductInterface;

interface EcommerceOrderProductMapperInterface
{
    public function mapFromOrderItem(OrderItemInterface $orderItem): EcommerceOrderProductInterface;
}
