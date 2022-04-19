<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\Contact;

use App\Entity\Channel\ChannelInterface;
use App\Entity\Customer\CustomerInterface;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Sylius\Component\Core\Model\ChannelInterface as SyliusChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface as SyliusCustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Exception\ChannelCustomerDoesNotExistException;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\ContactListMapperInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Contact\ContactListsSubscriber;
use Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\Contact\ContactListsSubscriberHandler;
use PhpSpec\ObjectBehavior;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ContactListInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Resolver\CustomerChannelsResolverInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Resolver\ListSubscriptionStatusResolverInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\CreateResourceResponseInterface;

class ContactListsSubscriberHandlerSpec extends ObjectBehavior
{
    public function let(
        CustomerRepositoryInterface $customerRepository,
        CustomerChannelsResolverInterface $customerChannelsResolver,
        ListSubscriptionStatusResolverInterface $listSubscriptionStatusResolver,
        ActiveCampaignResourceClientInterface $activeCampaignContactListClient,
        ContactListMapperInterface $contactListMapper,
        LoggerInterface $logger,
        CustomerInterface $customer,
        ChannelInterface $firstChannel,
        SyliusChannelInterface $secondChannel,
        ChannelInterface $thirdChannel,
        ChannelInterface $fourthChannel,
        ContactListInterface $firstContactList,
        ContactListInterface $fourthContactList,
        CreateResourceResponseInterface $createResourceResponse
    ): void {
        $customer->getEmail()->willReturn('info@email.com');
        $customer->getActiveCampaignId()->willReturn(134);
        $customerRepository->find(12)->willReturn($customer);
        $customerChannelsResolver->resolve($customer)->willReturn([$firstChannel, $secondChannel, $thirdChannel, $fourthChannel]);

        $listSubscriptionStatusResolver->resolve($customer, $firstChannel)->willReturn(ListSubscriptionStatusResolverInterface::SUBSCRIBED_STATUS_CODE);
        $listSubscriptionStatusResolver->resolve($customer, $fourthChannel)->willReturn(ListSubscriptionStatusResolverInterface::UNSUBSCRIBED_STATUS_CODE);

        $firstChannel->getCode()->willReturn('ecommerce');
        $firstChannel->getActiveCampaignListId()->willReturn(23);
        $thirdChannel->getActiveCampaignListId()->willReturn(null);
        $fourthChannel->getActiveCampaignListId()->willReturn(18);

        $contactListMapper->mapFromListContactStatusAndSourceId(23, 134, ListSubscriptionStatusResolverInterface::SUBSCRIBED_STATUS_CODE)->willReturn($firstContactList);
        $contactListMapper->mapFromListContactStatusAndSourceId(18, 134, ListSubscriptionStatusResolverInterface::UNSUBSCRIBED_STATUS_CODE)->willReturn($fourthContactList);

        $activeCampaignContactListClient->create($firstContactList)->willReturn($createResourceResponse);
        $activeCampaignContactListClient->create($fourthContactList)->willThrow(new HttpException(200));

        $this->beConstructedWith($customerRepository, $customerChannelsResolver, $listSubscriptionStatusResolver, $activeCampaignContactListClient, $contactListMapper, $logger);
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

    public function it_throws_if_http_exception_does_not_have_200_as_status_code(
        ActiveCampaignResourceClientInterface $activeCampaignContactListClient,
        ContactListInterface $fourthContactList
    ): void {
        $activeCampaignContactListClient->create($fourthContactList)->willThrow(new HttpException(400));

        $this->shouldThrow(new HttpException(400))->during(
            '__invoke',
            [new ContactListsSubscriber(12)]
        );
    }

    public function it_handles_customer_list_subscriptions(
        LoggerInterface $logger,
        ActiveCampaignResourceClientInterface $activeCampaignContactListClient,
        ContactListInterface $firstContactList,
        ContactListInterface $fourthContactList
    ): void {
        $activeCampaignContactListClient->create($firstContactList)->shouldBeCalledOnce();
        $activeCampaignContactListClient->create($fourthContactList)->shouldBeCalledOnce();

        $logger->info('The association with the list with id "18" already exists for the contact with id "134".')->shouldBeCalledOnce();

        $this->__invoke(new ContactListsSubscriber(12));
    }

    public function it_does_not_throw_if_contact_list_subscription_could_be_resolved(
        LoggerInterface $logger,
        ListSubscriptionStatusResolverInterface $listSubscriptionStatusResolver,
        CustomerInterface $customer,
        ChannelInterface $firstChannel
    ): void {
        $listSubscriptionStatusResolver->resolve($customer, $firstChannel)->willThrow(new ChannelCustomerDoesNotExistException('test'));
        $logger->info('Unable to resolve for the customer "info@email.com" the subscription status for the list "23" of channel "ecommerce".')->shouldBeCalledOnce();
        $logger->info('The association with the list with id "18" already exists for the contact with id "134".')->shouldBeCalledOnce();

        $this->__invoke(new ContactListsSubscriber(12));
    }
}
