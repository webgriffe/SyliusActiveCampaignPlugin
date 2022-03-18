<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\Mapper;

use App\Entity\Channel\ChannelInterface;
use App\Entity\Customer\CustomerInterface;
use DateTime;
use DateTimeInterface;
use Prophecy\Argument;
use Sylius\Component\Core\Model\ChannelInterface as SyliusChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface as SyliusCustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign\EcommerceOrderFactoryInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\EcommerceOrderMapper;
use PhpSpec\ObjectBehavior;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\EcommerceOrderMapperInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\EcommerceOrderInterface;
use Webmozart\Assert\InvalidArgumentException;

class EcommerceOrderMapperSpec extends ObjectBehavior
{
    public function let(
        EcommerceOrderFactoryInterface $ecommerceOrderFactory,
        OrderInterface $order,
        CustomerInterface $customer,
        ChannelInterface $channel,
        EcommerceOrderInterface $ecommerceOrder
    ): void {
        $order->getCustomer()->willReturn($customer);
        $order->getChannel()->willReturn($channel);
        $order->getCurrencyCode()->willReturn('EUR');
        $order->getCreatedAt()->willReturn(new DateTime('2022-03-18'));
        $order->getId()->willReturn(125);
        $order->getTotal()->willReturn(15450);

        $customer->getEmail()->willReturn('info@activecampaign.org');
        $customer->getActiveCampaignId()->willReturn(432);

        $channel->getActiveCampaignId()->willReturn(1);

        $ecommerceOrderFactory->createNew(
            'info@activecampaign.org',
            1,
            432,
            'EUR',
            15450,
            Argument::type(DateTimeInterface::class),
            '125',
            null,
            null
        )->willReturn($ecommerceOrder);

        $this->beConstructedWith($ecommerceOrderFactory);
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

    public function it_maps_ecommerce_order_from_order(
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
}
