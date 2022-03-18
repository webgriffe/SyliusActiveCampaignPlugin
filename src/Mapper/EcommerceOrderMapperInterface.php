<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Mapper;

use Sylius\Component\Core\Model\OrderInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\EcommerceOrderInterface;

interface EcommerceOrderMapperInterface
{
    public function mapFromOrder(OrderInterface $order, bool $isInRealTime): EcommerceOrderInterface;
}
