<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\Updater;

use App\Entity\Customer\CustomerInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Resolver\ListSubscriptionStatusResolverInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Updater\CustomerBasedListSubscriptionStatusUpdater;
use PhpSpec\ObjectBehavior;
use Webgriffe\SyliusActiveCampaignPlugin\Updater\ListSubscriptionStatusUpdaterInterface;

class CustomerBasedListSubscriptionStatusUpdaterSpec extends ObjectBehavior
{
    public function let(
        CustomerRepositoryInterface $customerRepository
    ): void {
        $this->beConstructedWith($customerRepository);
    }

    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(CustomerBasedListSubscriptionStatusUpdater::class);
    }

    public function it_implements_list_subscription_status_updater_interface(): void
    {
        $this->shouldImplement(ListSubscriptionStatusUpdaterInterface::class);
    }

    public function it_updates_subscribed_to_newsletter_status(
        CustomerInterface $customer,
        ChannelInterface $channel,
        CustomerRepositoryInterface $customerRepository
    ): void {
        $customer->setSubscribedToNewsletter(true)->shouldBeCalledOnce();
        $customerRepository->add($customer)->shouldBeCalledOnce();

        $this->update($customer, $channel, ListSubscriptionStatusResolverInterface::SUBSCRIBED_STATUS_CODE);
    }

    public function it_updates_unsubscribed_to_newsletter_status(
        CustomerInterface $customer,
        ChannelInterface $channel,
        CustomerRepositoryInterface $customerRepository
    ): void {
        $customer->setSubscribedToNewsletter(false)->shouldBeCalledOnce();
        $customerRepository->add($customer)->shouldBeCalledOnce();

        $this->update($customer, $channel, ListSubscriptionStatusResolverInterface::UNSUBSCRIBED_STATUS_CODE);
    }
}
