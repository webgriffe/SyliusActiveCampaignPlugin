<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\Mapper;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign\EcommerceOrderDiscountFactoryInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\EcommerceOrderDiscountMapper;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\EcommerceOrderDiscountMapperInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\EcommerceOrderDiscountInterface;

class EcommerceOrderDiscountMapperSpec extends ObjectBehavior
{
    public function let(
        EcommerceOrderDiscountFactoryInterface $ecommerceOrderDiscountFactory,
        OrderInterface $order,
        PromotionInterface $freeShipmentPromotion,
        PromotionInterface $winterSalePromotion,
        EcommerceOrderDiscountInterface $ecommerceOrderDiscount,
        AdjustmentInterface $firstShippingAdjustment,
        AdjustmentInterface $firstOrderUnitAdjustment,
        AdjustmentInterface $firstOrderItemAdjustment,
        AdjustmentInterface $firstOrderPromotionAdjustment,
    ): void {
        $ecommerceOrderDiscountFactory->createNew()->willReturn($ecommerceOrderDiscount);

        $freeShipmentPromotion->getCode()->willReturn('FREE_SHIPMENT');
        $freeShipmentPromotion->getName()->willReturn('Free Shipment');

        $winterSalePromotion->getCode()->willReturn('WINTER_SALE');
        $winterSalePromotion->getName()->willReturn('Winter Sale');

        $firstShippingAdjustment->getAmount()->willReturn(-5000);
        $firstShippingAdjustment->getOriginCode()->willReturn('FREE_SHIPMENT');
        $order->getAdjustmentsRecursively(AdjustmentInterface::ORDER_SHIPPING_PROMOTION_ADJUSTMENT)->willReturn(new ArrayCollection([
            $firstShippingAdjustment->getWrappedObject(),
        ]));

        $firstOrderUnitAdjustment->getAmount()->willReturn(-1600);
        $firstOrderUnitAdjustment->getOriginCode()->willReturn('WINTER_SALE');
        $order->getAdjustmentsRecursively(AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT)->willReturn(new ArrayCollection([
            $firstOrderUnitAdjustment->getWrappedObject(),
        ]));

        $firstOrderItemAdjustment->getAmount()->willReturn(-1350);
        $firstOrderItemAdjustment->getOriginCode()->willReturn('WINTER_SALE');
        $order->getAdjustmentsRecursively(AdjustmentInterface::ORDER_ITEM_PROMOTION_ADJUSTMENT)->willReturn(new ArrayCollection([
            $firstOrderItemAdjustment->getWrappedObject(),
        ]));

        $firstOrderPromotionAdjustment->getAmount()->willReturn(-2000);
        $firstOrderPromotionAdjustment->getOriginCode()->willReturn('WINTER_SALE');
        $order->getAdjustmentsRecursively(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT)->willReturn(new ArrayCollection([
            $firstOrderPromotionAdjustment->getWrappedObject(),
        ]));

        $this->beConstructedWith($ecommerceOrderDiscountFactory);
    }

    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(EcommerceOrderDiscountMapper::class);
    }

    public function it_implements_ecommerce_order_discount_mapper_interface(): void
    {
        $this->shouldImplement(EcommerceOrderDiscountMapperInterface::class);
    }

    public function it_maps_ecommerce_order_discount_from_order_promotion(
        PromotionInterface $winterSalePromotion,
        OrderInterface $order,
        EcommerceOrderDiscountInterface $ecommerceOrderDiscount
    ): void {
        $ecommerceOrderDiscount->setName('Winter Sale')->shouldBeCalledOnce();
        $ecommerceOrderDiscount->setDiscountAmount(4950)->shouldBeCalledOnce();
        $ecommerceOrderDiscount->setType(EcommerceOrderDiscountInterface::ORDER_DISCOUNT_TYPE)->shouldBeCalledOnce();

        $this->mapFromPromotion($order, $winterSalePromotion)->shouldReturn($ecommerceOrderDiscount);
    }

    public function it_maps_ecommerce_order_discount_from_shipment_promotion(
        PromotionInterface $freeShipmentPromotion,
        OrderInterface $order,
        EcommerceOrderDiscountInterface $ecommerceOrderDiscount
    ): void {
        $ecommerceOrderDiscount->setName('Free Shipment')->shouldBeCalledOnce();
        $ecommerceOrderDiscount->setDiscountAmount(5000)->shouldBeCalledOnce();
        $ecommerceOrderDiscount->setType(EcommerceOrderDiscountInterface::SHIPPING_DISCOUNT_TYPE)->shouldBeCalledOnce();

        $this->mapFromPromotion($order, $freeShipmentPromotion)->shouldReturn($ecommerceOrderDiscount);
    }

    public function it_maps_ecommerce_order_discount_as_order_promotion_if_code_is_for_both_order_and_shipment_promotion(
        PromotionInterface $freeShipmentPromotion,
        OrderInterface $order,
        EcommerceOrderDiscountInterface $ecommerceOrderDiscount
    ): void {
        $freeShipmentPromotion->getCode()->willReturn('WINTER_SALE');
        $ecommerceOrderDiscount->setName('Free Shipment')->shouldBeCalledOnce();
        $ecommerceOrderDiscount->setDiscountAmount(4950)->shouldBeCalledOnce();
        $ecommerceOrderDiscount->setType(EcommerceOrderDiscountInterface::ORDER_DISCOUNT_TYPE)->shouldBeCalledOnce();

        $this->mapFromPromotion($order, $freeShipmentPromotion)->shouldReturn($ecommerceOrderDiscount);
    }

    public function it_maps_ecommerce_order_discount_as_empty_promotion_if_any_adjustments_is_found(
        PromotionInterface $otherPromotion,
        OrderInterface $order,
        EcommerceOrderDiscountInterface $ecommerceOrderDiscount
    ): void {
        $otherPromotion->getCode()->willReturn('OTHER');
        $otherPromotion->getName()->willReturn('Other');
        $ecommerceOrderDiscount->setName('Other')->shouldBeCalledOnce();
        $ecommerceOrderDiscount->setDiscountAmount(0)->shouldNotBeCalled();
        $ecommerceOrderDiscount->setType(EcommerceOrderDiscountInterface::ORDER_DISCOUNT_TYPE)->shouldNotBeCalled();
        $ecommerceOrderDiscount->setType(EcommerceOrderDiscountInterface::SHIPPING_DISCOUNT_TYPE)->shouldNotBeCalled();

        $this->mapFromPromotion($order, $otherPromotion)->shouldReturn($ecommerceOrderDiscount);
    }
}
