<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\Mapper;

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
        ActiveCampaignAwareInterface $channel
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

    public function it_implements_contact_mapper_interface(): void
    {
        $this->shouldImplement(EcommerceCustomerMapperInterface::class);
    }

    public function it_throws_if_channel_does_not_have_an_active_campaign_id(CustomerInterface $customer, ActiveCampaignAwareInterface $channel): void
    {
        $channel->getActiveCampaignId()->willReturn(null);

        $this
            ->shouldThrow(ChannelConnectionNotSetException::class)
            ->during('mapFromCustomerAndChannel', [$customer, $channel]);
    }

    public function it_throws_if_customer_does_not_have_email(CustomerInterface $customer, ActiveCampaignAwareInterface $channel): void
    {
        $customer->getEmail()->willReturn(null);

        $this
            ->shouldThrow(CustomerDoesNotHaveEmailException::class)
            ->during('mapFromCustomerAndChannel', [$customer, $channel]);
    }

    public function it_returns_an_instance_of_active_campaign_contact(CustomerInterface $customer, ActiveCampaignAwareInterface $channel): void
    {
        $this->mapFromCustomerAndChannel($customer, $channel)->shouldReturnAnInstanceOf(EcommerceCustomerInterface::class);
    }

    public function it_returns_an_active_campaign_contact_mapped_by_customer(
        CustomerInterface $customer,
        EcommerceCustomerInterface $ecommerceCustomer,
        ActiveCampaignAwareInterface $channel
    ): void {
        $customer->isSubscribedToNewsletter()->willReturn(true);
        $ecommerceCustomer->setAcceptsMarketing('1')->shouldBeCalledOnce();

        $this->mapFromCustomerAndChannel($customer, $channel)->shouldReturn($ecommerceCustomer);
    }
}
