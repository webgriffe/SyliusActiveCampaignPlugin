<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Mapper;

use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign\EcommerceOrderDiscountFactoryInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\EcommerceOrderDiscountInterface;
use Webmozart\Assert\Assert;

final class EcommerceOrderDiscountMapper implements EcommerceOrderDiscountMapperInterface
{
    public function __construct(
        private EcommerceOrderDiscountFactoryInterface $ecommerceOrderDiscountFactory
    ) {
    }

    public function mapFromPromotion(OrderInterface $order, PromotionInterface $promotion): EcommerceOrderDiscountInterface
    {
        $ecommerceOrderDiscount = $this->ecommerceOrderDiscountFactory->createNew();
        $ecommerceOrderDiscount->setName($promotion->getName());
        $orderPromotionAdjustments = $this->getOrderPromotionAdjustments($order, $promotion);
        if (count($orderPromotionAdjustments) > 0) {
            $totalDiscountAmount = $this->getTotalDiscountAmountFromAdjustments($orderPromotionAdjustments);
            $ecommerceOrderDiscount->setDiscountAmount($totalDiscountAmount);
            $ecommerceOrderDiscount->setType(EcommerceOrderDiscountInterface::ORDER_DISCOUNT_TYPE);

            return $ecommerceOrderDiscount;
        }
        $shipmentPromotionAdjustments = $this->getShipmentPromotionAdjustments($order, $promotion);
        if (count($shipmentPromotionAdjustments) > 0) {
            $totalDiscountAmount = $this->getTotalDiscountAmountFromAdjustments($shipmentPromotionAdjustments);
            $ecommerceOrderDiscount->setDiscountAmount($totalDiscountAmount);
            $ecommerceOrderDiscount->setType(EcommerceOrderDiscountInterface::SHIPPING_DISCOUNT_TYPE);

            return $ecommerceOrderDiscount;
        }

        return $ecommerceOrderDiscount;
    }

    /** @return AdjustmentInterface[] */
    private function getOrderPromotionAdjustments(OrderInterface $order, PromotionInterface $promotion): array
    {
        $allOrderPromotionAdjustments = array_merge(
            $order->getAdjustmentsRecursively(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT)->toArray(),
            $order->getAdjustmentsRecursively(AdjustmentInterface::ORDER_ITEM_PROMOTION_ADJUSTMENT)->toArray(),
            $order->getAdjustmentsRecursively(AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT)->toArray(),
        );
        Assert::allIsInstanceOf($allOrderPromotionAdjustments, AdjustmentInterface::class);

        return array_filter($allOrderPromotionAdjustments, static function (AdjustmentInterface $adjustment) use ($promotion): bool {
            return $adjustment->getOriginCode() === $promotion->getCode();
        });
    }

    /** @return AdjustmentInterface[] */
    private function getShipmentPromotionAdjustments(OrderInterface $order, PromotionInterface $promotion): array
    {
        $allShipmentPromotionAdjustments = $order->getAdjustmentsRecursively(AdjustmentInterface::ORDER_SHIPPING_PROMOTION_ADJUSTMENT)->toArray();
        Assert::allIsInstanceOf($allShipmentPromotionAdjustments, AdjustmentInterface::class);

        return array_filter($allShipmentPromotionAdjustments, static function (AdjustmentInterface $adjustment) use ($promotion): bool {
            return $adjustment->getOriginCode() === $promotion->getCode();
        });
    }

    /** @param AdjustmentInterface[] $promotionAdjustments */
    private function getTotalDiscountAmountFromAdjustments(array $promotionAdjustments): int
    {
        $totalDiscountAmount = 0;
        foreach ($promotionAdjustments as $promotionAdjustment) {
            $totalDiscountAmount += $promotionAdjustment->getAmount();
        }

        return $totalDiscountAmount;
    }
}
