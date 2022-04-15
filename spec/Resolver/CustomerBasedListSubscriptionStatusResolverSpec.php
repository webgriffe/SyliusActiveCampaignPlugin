<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\Resolver;

use App\Entity\Channel\ChannelInterface;
use App\Entity\Customer\CustomerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Exception\ChannelListIdDoesNotExistException;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ChannelCustomerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Resolver\CustomerBasedListSubscriptionStatusResolver;
use PhpSpec\ObjectBehavior;
use Webgriffe\SyliusActiveCampaignPlugin\Resolver\ListSubscriptionStatusResolverInterface;

class CustomerBasedListSubscriptionStatusResolverSpec extends ObjectBehavior
{
    public function let(
        CustomerInterface $customer,
        ChannelInterface $channel,
        ChannelCustomerInterface $channelCustomer
    ): void {
        $channel->getCode()->willReturn('ecommerce');
        $channel->getActiveCampaignListId()->willReturn(15);

        $customer->isSubscribedToNewsletter()->willReturn(true);

        $this->beConstructedWith();
    }

    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(CustomerBasedListSubscriptionStatusResolver::class);
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

    public function it_returns_subscribed_if_customer_is_subscribed_to_newsletter(
        CustomerInterface $customer,
        ChannelInterface $channel
    ): void {
        $this->resolve($customer, $channel)->shouldReturn(ListSubscriptionStatusResolverInterface::SUBSCRIBED_STATUS_CODE);
    }
}
