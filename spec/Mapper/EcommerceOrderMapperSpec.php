<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\Mapper;

use App\Entity\Channel\ChannelInterface;
use App\Entity\Customer\CustomerInterface;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Prophecy\Argument;
use Sylius\Component\Core\Model\ChannelInterface as SyliusChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface as SyliusCustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Symfony\Component\Routing\RouterInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign\EcommerceOrderFactoryInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\EcommerceOrderDiscountMapperInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\EcommerceOrderMapper;
use PhpSpec\ObjectBehavior;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\EcommerceOrderMapperInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\EcommerceOrderProductMapperInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\EcommerceOrderDiscountInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\EcommerceOrderInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\EcommerceOrderProductInterface;
use Webmozart\Assert\InvalidArgumentException;

class EcommerceOrderMapperSpec extends ObjectBehavior
{
    public function let(
        EcommerceOrderFactoryInterface $ecommerceOrderFactory,
        RouterInterface $router,
        EcommerceOrderProductMapperInterface $ecommerceOrderProductMapper,
        EcommerceOrderDiscountMapperInterface $ecommerceOrderDiscountMapper,
        OrderInterface $order,
        CustomerInterface $customer,
        ChannelInterface $channel,
        EcommerceOrderInterface $ecommerceOrder,
        ShipmentInterface $firstShipment,
        ShipmentInterface $secondShipment,
        ShippingMethodInterface $firstShippingMethod,
        ShippingMethodInterface $secondShippingMethod,
        OrderItemInterface $firstOrderItem,
        EcommerceOrderProductInterface $firstOrderProduct,
        PromotionInterface $firstPromotion,
        EcommerceOrderDiscountInterface $firstOrderDiscount
    ): void {
        $router->generate('sylius_shop_order_show', ['tokenValue' => 'sD4ew_w4s5T', '_locale' => 'en_US'])->willReturn('https://localhost/order/sD4ew_w4s5T');

        $ecommerceOrderProductMapper->mapFromOrderItem($firstOrderItem)->willReturn($firstOrderProduct);

        $ecommerceOrderDiscountMapper->mapFromPromotion($order, $firstPromotion)->willReturn($firstOrderDiscount);

        $order->getCustomer()->willReturn($customer);
        $order->getChannel()->willReturn($channel);
        $order->getState()->willReturn(OrderInterface::STATE_NEW);
        $order->getLocaleCode()->willReturn('en_US');
        $order->getCurrencyCode()->willReturn('EUR');
        $order->getCreatedAt()->willReturn(new DateTime('2022-03-18'));
        $order->getUpdatedAt()->willReturn(new DateTime('2022-03-19'));
        $order->getId()->willReturn(125);
        $order->getTotal()->willReturn(15450);
        $order->getShippingTotal()->willReturn(1000);
        $order->getTaxTotal()->willReturn(2500);
        $order->getOrderPromotionTotal()->willReturn(1200);
        $order->getShipments()->willReturn(new ArrayCollection([$firstShipment->getWrappedObject(), $secondShipment->getWrappedObject()]));
        $order->getTokenValue()->willReturn('sD4ew_w4s5T');
        $order->getNumber()->willReturn('00000234');
        $order->getItems()->willReturn(new ArrayCollection([$firstOrderItem->getWrappedObject()]));
        $order->getPromotions()->willReturn(new ArrayCollection([$firstPromotion->getWrappedObject()]));

        $firstShipment->getMethod()->willReturn($firstShippingMethod);
        $secondShipment->getMethod()->willReturn($secondShippingMethod);

        $firstShippingMethod->getName()->willReturn('UPS Delivery');
        $secondShippingMethod->getName()->willReturn('DHL');

        $customer->getEmail()->willReturn('info@activecampaign.org');
        $customer->getActiveCampaignId()->willReturn(432);

        $channel->getActiveCampaignId()->willReturn(1);

        $ecommerceOrder->setShippingAmount(1000);
        $ecommerceOrder->setTaxAmount(2500);
        $ecommerceOrder->setDiscountAmount(1200);
        $ecommerceOrder->setShippingMethod('UPS Delivery');
        $ecommerceOrder->setShippingMethod('DHL');
        $ecommerceOrder->setOrderUrl('https://localhost/order/sD4ew_w4s5T');
        $ecommerceOrder->setExternalUpdatedDate(Argument::type(DateTimeInterface::class));
        $ecommerceOrder->setOrderNumber('00000234');
        $ecommerceOrder->setOrderProducts([$firstOrderProduct]);
        $ecommerceOrder->setOrderDiscounts([$firstOrderDiscount]);

        $ecommerceOrderFactory->createNew(
            'info@activecampaign.org',
            '1',
            '432',
            'EUR',
            15450,
            Argument::type(DateTimeInterface::class),
            '125',
            null,
            null
        )->willReturn($ecommerceOrder);

        $this->beConstructedWith($ecommerceOrderFactory, $router, $ecommerceOrderProductMapper, $ecommerceOrderDiscountMapper);
    }

    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(EcommerceOrderMapper::class);
    }

    public function it_implements_ecommerce_order_mapper_interface(): void
    {
        $this->shouldImplement(EcommerceOrderMapperInterface::class);
    }

    public function it_throws_if_order_customer_is_not_an_instance_of_sylius_customer_interface(OrderInterface $order): void
    {
        $order->getCustomer()->willReturn(null);
        $this->shouldThrow(new InvalidArgumentException('Order customer should implement "Sylius\Component\Core\Model\CustomerInterface".'))
            ->during('mapFromOrder', [$order, true]);
    }

    public function it_throws_if_order_customer_is_not_an_instance_of_active_campaign_aware_interface(OrderInterface $order, SyliusCustomerInterface $syliusCustomer): void
    {
        $order->getCustomer()->willReturn($syliusCustomer);
        $this->shouldThrow(new InvalidArgumentException('Order customer should implement "Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface".'))
            ->during('mapFromOrder', [$order, true]);
    }

    public function it_throws_if_order_customer_email_is_null(OrderInterface $order, CustomerInterface $customer): void
    {
        $customer->getEmail()->willReturn(null);
        $this->shouldThrow(new InvalidArgumentException('The customer\'s email should not be null.'))
            ->during('mapFromOrder', [$order, true]);
    }

    public function it_throws_if_order_customer_active_campaign_id_is_null(OrderInterface $order, CustomerInterface $customer): void
    {
        $customer->getActiveCampaignId()->willReturn(null);
        $this->shouldThrow(new InvalidArgumentException('The customer\'s ActiveCampaign customer id should not be null.'))
            ->during('mapFromOrder', [$order, true]);
    }

    public function it_throws_if_order_channel_is_null(OrderInterface $order): void
    {
        $order->getChannel()->willReturn(null);
        $this->shouldThrow(new InvalidArgumentException('Order does not have a channel.'))
            ->during('mapFromOrder', [$order, true]);
    }

    public function it_throws_if_order_channel_is_not_an_instance_of_active_campaign_aware_interface(OrderInterface $order, SyliusChannelInterface $syliusChannel): void
    {
        $order->getChannel()->willReturn($syliusChannel);
        $this->shouldThrow(new InvalidArgumentException('Order channel should implement "Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface".'))
            ->during('mapFromOrder', [$order, true]);
    }

    public function it_throws_if_order_channel_active_campaign_id_is_null(OrderInterface $order, ChannelInterface $channel): void
    {
        $channel->getActiveCampaignId()->willReturn(null);
        $this->shouldThrow(new InvalidArgumentException('The channel\'s ActiveCampaign connection id should not be null.'))
            ->during('mapFromOrder', [$order, true]);
    }

    public function it_throws_if_order_currency_code_is_null(OrderInterface $order): void
    {
        $order->getCurrencyCode()->willReturn(null);
        $this->shouldThrow(new InvalidArgumentException('The order currency code should not be null.'))
            ->during('mapFromOrder', [$order, true]);
    }

    public function it_throws_if_order_created_at_is_null(OrderInterface $order): void
    {
        $order->getCreatedAt()->willReturn(null);
        $this->shouldThrow(new InvalidArgumentException('The order creation date should not be null.'))
            ->during('mapFromOrder', [$order, true]);
    }

    public function it_throws_if_order_id_is_null(OrderInterface $order): void
    {
        $order->getId()->willReturn(null);
        $this->shouldThrow(new InvalidArgumentException('The order id should not be null.'))
            ->during('mapFromOrder', [$order, true]);
    }

    public function it_maps_ecommerce_order_without_shipping_method_if_order_has_no_shipments(
        OrderInterface $order,
        EcommerceOrderInterface $ecommerceOrder
    ): void {
        $order->getShipments()->willReturn(new ArrayCollection());
        $ecommerceOrder->setShippingMethod('UPS Delivery')->shouldNotBeCalled();
        $ecommerceOrder->setShippingMethod('DHL')->shouldNotBeCalled();

        $this->mapFromOrder($order, true)->shouldReturn($ecommerceOrder);
    }

    public function it_maps_ecommerce_order_without_shipping_method_if_order_first_shipment_does_not_have_shipping_method(
        OrderInterface $order,
        ShipmentInterface $shipment,
        EcommerceOrderInterface $ecommerceOrder
    ): void {
        $order->getShipments()->willReturn(new ArrayCollection([$shipment->getWrappedObject()]));
        $shipment->getMethod()->willReturn(null);
        $ecommerceOrder->setShippingMethod('UPS Delivery')->shouldNotBeCalled();
        $ecommerceOrder->setShippingMethod('DHL')->shouldNotBeCalled();

        $this->mapFromOrder($order, true)->shouldReturn($ecommerceOrder);
    }

    public function it_maps_ecommerce_order_real_time_from_order(
        OrderInterface $order,
        EcommerceOrderInterface $ecommerceOrder
    ): void {
        $ecommerceOrder->setSource(EcommerceOrderInterface::HISTORICAL_SOURCE_CODE)->shouldNotBeCalled();

        $this->mapFromOrder($order, true)->shouldReturn($ecommerceOrder);
    }

    public function it_maps_ecommerce_order_historical_from_order(
        OrderInterface $order,
        EcommerceOrderInterface $ecommerceOrder
    ): void {
        $ecommerceOrder->setSource(EcommerceOrderInterface::HISTORICAL_SOURCE_CODE)->shouldBeCalledOnce();

        $this->mapFromOrder($order, false)->shouldReturn($ecommerceOrder);
    }

    public function it_maps_ecommerce_abandoned_cart_from_order(
        OrderInterface $order,
        EcommerceOrderInterface $ecommerceOrder,
        EcommerceOrderFactoryInterface $ecommerceOrderFactory
    ): void {
        $order->getState()->willReturn(OrderInterface::STATE_CART);
        $ecommerceOrderFactory->createNew(
            'info@activecampaign.org',
            '1',
            '432',
            'EUR',
            15450,
            Argument::type(DateTimeInterface::class),
            null,
            '125',
            Argument::type(DateTimeInterface::class),
        )->willReturn($ecommerceOrder);

        $this->mapFromOrder($order, true)->shouldReturn($ecommerceOrder);
    }
}
