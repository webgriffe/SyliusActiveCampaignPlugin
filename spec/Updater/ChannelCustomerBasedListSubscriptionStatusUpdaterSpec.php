<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\Updater;

use Tests\Webgriffe\SyliusActiveCampaignPlugin\App\Entity\Customer\CustomerInterface;
use InvalidArgumentException;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface as SyliusCustomerInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ContactListInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ChannelCustomerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Updater\ChannelCustomerBasedListSubscriptionStatusUpdater;
use PhpSpec\ObjectBehavior;
use Webgriffe\SyliusActiveCampaignPlugin\Updater\ListSubscriptionStatusUpdaterInterface;

class ChannelCustomerBasedListSubscriptionStatusUpdaterSpec extends ObjectBehavior
{
    public function let(
        RepositoryInterface $channelCustomerRepository,
        CustomerInterface $customer,
        ChannelInterface $channel,
        ChannelCustomerInterface $channelCustomer
    ): void {
        $customer->getEmail()->willReturn('info@email.com');
        $channel->getCode()->willReturn('ecommerce');
        $customer->getChannelCustomerByChannel($channel)->willReturn($channelCustomer);

        $this->beConstructedWith($channelCustomerRepository);
    }

    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(ChannelCustomerBasedListSubscriptionStatusUpdater::class);
    }

    public function it_implements_list_subscription_status_updater_interface(): void
    {
        $this->shouldImplement(ListSubscriptionStatusUpdaterInterface::class);
    }

    public function it_throws_if_customer_is_not_an_active_campaign_customer_interface(SyliusCustomerInterface $syliusCustomer, ChannelInterface $channel): void
    {
        $this->shouldThrow(new InvalidArgumentException('The customer should implements the "Webgriffe\SyliusActiveCampaignPlugin\Model\CustomerActiveCampaignAwareInterface" interface.'))->during(
            'update',
            [$syliusCustomer, $channel, ContactListInterface::SUBSCRIBED_STATUS_CODE]
        );
    }

    public function it_throws_if_customer_does_not_have_association_with_channel(CustomerInterface $customer, ChannelInterface $channel): void
    {
        $customer->getChannelCustomerByChannel($channel)->willReturn(null);

        $this->shouldThrow(new InvalidArgumentException('The customer "info@email.com" does not have an association with the channel "ecommerce".'))->during(
            'update',
            [$customer, $channel, ContactListInterface::SUBSCRIBED_STATUS_CODE]
        );
    }

    public function it_updates_list_status_subscription(
        CustomerInterface $customer,
        ChannelInterface $channel,
        ChannelCustomerInterface $channelCustomer,
        RepositoryInterface $channelCustomerRepository
    ): void {
        $channelCustomer->setListSubscriptionStatus(ContactListInterface::SUBSCRIBED_STATUS_CODE)->shouldBeCalledOnce();
        $channelCustomerRepository->add($channelCustomer)->shouldBeCalledOnce();

        $this->update($customer, $channel, ContactListInterface::SUBSCRIBED_STATUS_CODE);
    }
}
