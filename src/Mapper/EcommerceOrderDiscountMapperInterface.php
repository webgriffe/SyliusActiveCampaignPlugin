<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Mapper;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\EcommerceOrderDiscountInterface;

interface EcommerceOrderDiscountMapperInterface
{
    public function mapFromPromotion(OrderInterface $order, PromotionInterface $promotion): EcommerceOrderDiscountInterface;
}
