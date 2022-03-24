<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\Mapper;

use App\Entity\Channel\ChannelInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\CustomerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Exception\ChannelConnectionNotSetException;
use Webgriffe\SyliusActiveCampaignPlugin\Exception\CustomerDoesNotHaveEmailException;
use Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign\EcommerceCustomerFactoryInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\EcommerceCustomerMapper;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\EcommerceCustomerMapperInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\EcommerceCustomerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface;

class EcommerceCustomerMapperSpec extends ObjectBehavior
{
    public function let(
        EcommerceCustomerFactoryInterface $factory,
        CustomerInterface $customer,
        EcommerceCustomerInterface $ecommerceCustomer,
        ChannelInterface $channel
    ): void {
        $channel->getActiveCampaignId()->willReturn('10');

        $customer->getEmail()->willReturn('customer@domain.org');
        $customer->getId()->willReturn(512);
        $customer->isSubscribedToNewsletter()->willReturn(false);

        $factory->createNew('customer@domain.org', '10', '512')->willReturn($ecommerceCustomer);

        $this->beConstructedWith($factory);
    }

    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(EcommerceCustomerMapper::class);
    }

    public function it_implements_ecommerce_customer_mapper_interface(): void
    {
        $this->shouldImplement(EcommerceCustomerMapperInterface::class);
    }

    public function it_throws_if_channel_does_not_have_an_active_campaign_id(CustomerInterface $customer, ChannelInterface $channel): void
    {
        $channel->getActiveCampaignId()->willReturn(null);
        $channel->getCode()->shouldBeCalledOnce()->willReturn('ecommerce');

        $this
            ->shouldThrow(new ChannelConnectionNotSetException('Unable to create a new ActiveCampaign Ecommerce Customer, the channel "ecommerce" does not have a connection id. Please, create the connection from the channel before create the ecommerce customer for the channel.'))
            ->during('mapFromCustomerAndChannel', [$customer, $channel]);
    }

    public function it_throws_if_customer_does_not_have_email(CustomerInterface $customer, ActiveCampaignAwareInterface $channel): void
    {
        $customer->getEmail()->willReturn(null);

        $this
            ->shouldThrow(new CustomerDoesNotHaveEmailException('Unable to create a new ActiveCampaign Ecommerce Customer, the customer "512" does not have a valid email.'))
            ->during('mapFromCustomerAndChannel', [$customer, $channel]);
    }

    public function it_returns_an_instance_of_active_campaign_ecommerce_customer(CustomerInterface $customer, ChannelInterface $channel): void
    {
        $this->mapFromCustomerAndChannel($customer, $channel)->shouldReturnAnInstanceOf(EcommerceCustomerInterface::class);
    }

    public function it_returns_an_active_campaign_ecommerce_mapped_by_customer_and_channel(
        CustomerInterface $customer,
        EcommerceCustomerInterface $ecommerceCustomer,
        ChannelInterface $channel
    ): void {
        $customer->isSubscribedToNewsletter()->willReturn(true);
        $ecommerceCustomer->setAcceptsMarketing('1')->shouldBeCalledOnce();

        $this->mapFromCustomerAndChannel($customer, $channel)->shouldReturn($ecommerceCustomer);
    }
}
