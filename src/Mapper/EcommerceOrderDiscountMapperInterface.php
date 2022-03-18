<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Mapper;

use Sylius\Component\Promotion\Model\PromotionInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\EcommerceOrderDiscountInterface;

interface EcommerceOrderDiscountMapperInterface
{
    public function mapFromPromotion(PromotionInterface $promotion): EcommerceOrderDiscountInterface;
}
