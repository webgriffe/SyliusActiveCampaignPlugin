<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\Resolver;

use App\Entity\Channel\ChannelInterface;
use App\Entity\Customer\CustomerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Exception\ChannelCustomerDoesNotExistException;
use Webgriffe\SyliusActiveCampaignPlugin\Exception\ChannelListIdDoesNotExistException;
use Webgriffe\SyliusActiveCampaignPlugin\Exception\CustomerListSubscriptionStatusNotDefinedException;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ChannelCustomerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Resolver\ChannelCustomerBasedListSubscriptionStatusResolver;
use PhpSpec\ObjectBehavior;
use Webgriffe\SyliusActiveCampaignPlugin\Resolver\ListSubscriptionStatusResolverInterface;

class ChannelCustomerBasedListSubscriptionStatusResolverSpec extends ObjectBehavior
{
    public function let(
        CustomerInterface $customer,
        ChannelInterface $channel,
        ChannelCustomerInterface $channelCustomer
    ): void {
        $channel->getCode()->willReturn('ecommerce');
        $channel->getActiveCampaignListId()->willReturn(15);

        $customer->getEmail()->willReturn('info@email.com');
        $customer->getChannelCustomerByChannel($channel)->willReturn($channelCustomer);

        $this->beConstructedWith();
    }

    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(ChannelCustomerBasedListSubscriptionStatusResolver::class);
    }

    public function it_implements_list_subscription_status_resolver_interface(): void
    {
        $this->shouldImplement(ListSubscriptionStatusResolverInterface::class);
    }

    public function it_throws_if_channel_active_campaign_list_id_is_null(CustomerInterface $customer, ChannelInterface $channel): void
    {
        $channel->getActiveCampaignListId()->willReturn(null);

        $this->shouldThrow(new ChannelListIdDoesNotExistException('The channel "ecommerce" does not have a list id.'))->during(
            'resolve',
            [$customer, $channel]
        );
    }

    public function it_throws_if_channel_customer_association_does_not_exist(CustomerInterface $customer, ChannelInterface $channel): void
    {
        $customer->getChannelCustomerByChannel($channel)->willReturn(null);

        $this->shouldThrow(new ChannelCustomerDoesNotExistException('The customer "info@email.com" is not related with the channel "ecommerce".'))->during(
            'resolve',
            [$customer, $channel]
        );
    }

    public function it_throws_if_list_subscription_status_on_channel_customer_association_is_null(
        CustomerInterface $customer,
        ChannelInterface $channel,
        ChannelCustomerInterface $channelCustomer
    ): void {
        $channelCustomer->getListSubscriptionStatus()->willReturn(null);

        $this->shouldThrow(new CustomerListSubscriptionStatusNotDefinedException('The list subscription status for list of channel "ecommerce" of the customer "info@email.com" is not defined.'))->during(
            'resolve',
            [$customer, $channel]
        );
    }

    public function it_returns_list_subscription_status(
        CustomerInterface $customer,
        ChannelInterface $channel,
        ChannelCustomerInterface $channelCustomer
    ): void {
        $channelCustomer->getListSubscriptionStatus()->willReturn(ListSubscriptionStatusResolverInterface::SUBSCRIBED_STATUS_CODE);

        $this->resolve($customer, $channel)->shouldReturn(ListSubscriptionStatusResolverInterface::SUBSCRIBED_STATUS_CODE);
    }
}
