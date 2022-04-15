<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\Contact;

use App\Entity\Channel\ChannelInterface;
use App\Entity\Customer\CustomerInterface;
use InvalidArgumentException;
use Sylius\Component\Core\Model\ChannelInterface as SyliusChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface as SyliusCustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Contact\ContactListsSubscriber;
use Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\Contact\ContactListsSubscriberHandler;
use PhpSpec\ObjectBehavior;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Resolver\CustomerChannelsResolverInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Resolver\ListSubscriptionStatusResolverInterface;

class ContactListsSubscriberHandlerSpec extends ObjectBehavior
{
    public function let(
        CustomerRepositoryInterface $customerRepository,
        CustomerChannelsResolverInterface $customerChannelsResolver,
        ListSubscriptionStatusResolverInterface $listSubscriptionStatusResolver,
        CustomerInterface $customer,
        ChannelInterface $firstChannel,
        SyliusChannelInterface $secondChannel,
    ): void {
        $customer->getActiveCampaignId()->willReturn(134);
        $customerRepository->find(12)->willReturn($customer);
        $customerChannelsResolver->resolve($customer)->willReturn([$firstChannel, $secondChannel]);

        $this->beConstructedWith($customerRepository, $customerChannelsResolver, $listSubscriptionStatusResolver);
    }

    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(ContactListsSubscriberHandler::class);
    }

    public function it_throws_if_customer_is_not_found(
        CustomerRepositoryInterface $customerRepository
    ): void {
        $customerRepository->find(12)->shouldBeCalledOnce()->willReturn(null);

        $this->shouldThrow(new InvalidArgumentException('Customer with id "12" does not exists.'))->during(
            '__invoke',
            [new ContactListsSubscriber(12)]
        );
    }

    public function it_throws_if_customer_is_not_an_implementation_of_customer_active_campaign_aware_interface(
        CustomerRepositoryInterface $customerRepository,
        SyliusCustomerInterface $syliusCustomer
    ): void {
        $customerRepository->find(12)->shouldBeCalledOnce()->willReturn($syliusCustomer);

        $this->shouldThrow(new InvalidArgumentException('The Customer entity should implement the "Webgriffe\SyliusActiveCampaignPlugin\Model\CustomerActiveCampaignAwareInterface" class.'))->during(
            '__invoke',
            [new ContactListsSubscriber(12)]
        );
    }

    public function it_throws_if_customer_has_not_been_exported_to_active_campaign_yet(
        ActiveCampaignAwareInterface $customer
    ): void {
        $customer->getActiveCampaignId()->willReturn(null);

        $this->shouldThrow(new InvalidArgumentException('The Customer with id "12" does not have an ActiveCampaign id.'))->during(
            '__invoke',
            [new ContactListsSubscriber(12)]
        );
    }

    public function it_handles_customer_list_subscriptions(
        CustomerInterface $customer,
        ChannelInterface $firstChannel,
        SyliusChannelInterface $secondChannel,
        ListSubscriptionStatusResolverInterface $listSubscriptionStatusResolver,
    ): void {
        $listSubscriptionStatusResolver->resolve($customer, $firstChannel)->shouldBeCalledOnce()->willReturn(true);
        $listSubscriptionStatusResolver->resolve($customer, $secondChannel)->shouldNotBeCalled();

        $this->__invoke(new ContactListsSubscriber(12));
    }
}
