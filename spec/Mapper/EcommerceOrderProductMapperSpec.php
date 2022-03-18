<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\Mapper;

use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign\EcommerceOrderProductFactoryInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\EcommerceOrderProductMapper;
use PhpSpec\ObjectBehavior;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\EcommerceOrderProductMapperInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\EcommerceOrderProductInterface;
use Webmozart\Assert\InvalidArgumentException;

class EcommerceOrderProductMapperSpec extends ObjectBehavior
{
    public function let(
        EcommerceOrderProductFactoryInterface $ecommerceOrderProductFactory,
        OrderItemInterface $orderItem,
        ProductInterface $product
    ): void {
        $orderItem->getProductName()->willReturn('Wine bottle');
        $orderItem->getProduct()->willReturn($product);
        $orderItem->getUnitPrice()->willReturn(1200);
        $orderItem->getQuantity()->willReturn(2);

        $product->getId()->willReturn(432);

        $this->beConstructedWith($ecommerceOrderProductFactory);
    }

    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(EcommerceOrderProductMapper::class);
    }

    public function it_implements_ecommerce_order_product_mapper_interface(): void
    {
        $this->shouldImplement(EcommerceOrderProductMapperInterface::class);
    }

    public function it_throws_if_order_item_product_name_is_null(OrderItemInterface $orderItem): void
    {
        $orderItem->getProductName()->willReturn(null);
        $this->shouldThrow(new InvalidArgumentException('The order item\'s product name should not be null.'))
            ->during('mapFromOrderItem', [$orderItem]);
    }

    public function it_throws_if_order_item_product_is_null(OrderItemInterface $orderItem): void
    {
        $orderItem->getProduct()->willReturn(null);
        $this->shouldThrow(new InvalidArgumentException('The order item\'s product should not be null.'))
            ->during('mapFromOrderItem', [$orderItem]);
    }

    public function it_throws_if_order_item_product_id_is_null(OrderItemInterface $orderItem, ProductInterface $product): void
    {
        $product->getId()->willReturn(null);
        $this->shouldThrow(new InvalidArgumentException('The order item\'s product id should not be null.'))
            ->during('mapFromOrderItem', [$orderItem]);
    }

    public function it_maps_ecommerce_order_product_from_order_item(
        OrderItemInterface $orderItem,
        EcommerceOrderProductFactoryInterface $ecommerceOrderProductFactory,
        EcommerceOrderProductInterface $ecommerceOrderProduct
    ): void {
        $ecommerceOrderProductFactory->createNew('Wine bottle', 1200, 2, '432')->shouldBeCalledOnce()->willReturn($ecommerceOrderProduct);

        $this->mapFromOrderItem($orderItem)->shouldReturn($ecommerceOrderProduct);
    }
}
