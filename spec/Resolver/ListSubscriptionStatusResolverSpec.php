<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\Resolver;

use App\Entity\Channel\ChannelInterface;
use App\Entity\Customer\CustomerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ChannelCustomerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Resolver\ListSubscriptionStatusResolver;
use PhpSpec\ObjectBehavior;
use Webgriffe\SyliusActiveCampaignPlugin\Resolver\ListSubscriptionStatusResolverInterface;

class ListSubscriptionStatusResolverSpec extends ObjectBehavior
{
    public function let(
        CustomerInterface $customer,
        ChannelInterface $channel,
        ChannelCustomerInterface $channelCustomer
    ): void {
        $channel->getActiveCampaignListId()->willReturn(15);

        $customer->getChannelCustomerByChannel($channel)->willReturn($channelCustomer);

        $this->beConstructedWith();
    }

    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(ListSubscriptionStatusResolver::class);
    }

    public function it_implements_list_subscription_status_resolver_interface(): void
    {
        $this->shouldImplement(ListSubscriptionStatusResolverInterface::class);
    }

    public function it_returns_false_if_channel_active_campaign_list_id_is_null(CustomerInterface $customer, ChannelInterface $channel): void
    {
        $channel->getActiveCampaignListId()->willReturn(null);

        $this->resolve($customer, $channel)->shouldReturn(false);
    }

    public function it_returns_false_if_channel_customer_association_does_not_exist(CustomerInterface $customer, ChannelInterface $channel): void
    {
        $customer->getChannelCustomerByChannel($channel)->willReturn(null);

        $this->resolve($customer, $channel)->shouldReturn(false);
    }

    public function it_returns_false_if_list_subscription_status_on_channel_customer_association_is_null(
        CustomerInterface $customer,
        ChannelInterface $channel,
        ChannelCustomerInterface $channelCustomer
    ): void {
        $channelCustomer->getListSubscriptionStatus()->willReturn(null);

        $this->resolve($customer, $channel)->shouldReturn(false);
    }

    public function it_returns_false_if_list_subscription_status_on_channel_customer_association_is_unsubscribed(
        CustomerInterface $customer,
        ChannelInterface $channel,
        ChannelCustomerInterface $channelCustomer
    ): void {
        $channelCustomer->getListSubscriptionStatus()->willReturn(ChannelCustomerInterface::UNSUBSCRIBED_FROM_CONTACT_LIST);

        $this->resolve($customer, $channel)->shouldReturn(false);
    }

    public function it_returns_true_if_list_subscription_status_on_channel_customer_association_is_subscribed(
        CustomerInterface $customer,
        ChannelInterface $channel,
        ChannelCustomerInterface $channelCustomer
    ): void {
        $channelCustomer->getListSubscriptionStatus()->willReturn(ChannelCustomerInterface::SUBSCRIBED_TO_CONTACT_LIST);

        $this->resolve($customer, $channel)->shouldReturn(true);
    }
}
