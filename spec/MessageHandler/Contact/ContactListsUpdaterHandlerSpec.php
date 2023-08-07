<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\Contact;

use Tests\Webgriffe\SyliusActiveCampaignPlugin\App\Entity\Channel\ChannelInterface;
use Tests\Webgriffe\SyliusActiveCampaignPlugin\App\Entity\Customer\CustomerInterface;
use InvalidArgumentException;
use LogicException;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\CustomerInterface as SyliusCustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Contact\ContactListsUpdater;
use Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\Contact\ContactListsUpdaterHandler;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ContactListInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Resolver\CustomerChannelsResolverInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Resolver\ListSubscriptionStatusResolverInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Updater\ListSubscriptionStatusUpdaterInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Contact\RetrieveContactResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ResourceResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\RetrieveResourceResponseInterface;

class ContactListsUpdaterHandlerSpec extends ObjectBehavior
{
    public function let(
        CustomerRepositoryInterface $customerRepository,
        CustomerChannelsResolverInterface $customerChannelsResolver,
        ActiveCampaignResourceClientInterface $activeCampaignContactClient,
        ListSubscriptionStatusUpdaterInterface $listSubscriptionStatusUpdater,
        CustomerInterface $customer,
        RetrieveContactResponseInterface $retrieveResourceResponse,
        ResourceResponseInterface $resourceResponse,
        ChannelInterface $channel1,
        ChannelInterface $channel2
    ): void {
        $customerRepository->find(12)->willReturn($customer);

        $customer->getActiveCampaignId()->willReturn(134);

        $activeCampaignContactClient->get(134)->willReturn($retrieveResourceResponse);

        $retrieveResourceResponse->getResourceResponse()->willReturn($resourceResponse);
        $resourceResponse->getId()->willReturn(134);
        $retrieveResourceResponse->getContactLists()->willReturn([
            ['contact' => '134', 'list' => '3', 'status' => '1', 'id' => '1'],
            ['contact' => '134', 'list' => '5', 'status' => '2', 'id' => '1'],
            ['contact' => '134', 'list' => '6', 'status' => '1', 'id' => '1'],
        ]);

        $channel1->getActiveCampaignListId()->willReturn(3);
        $channel2->getActiveCampaignListId()->willReturn(5);

        $customerChannelsResolver->resolve($customer)->willReturn([$channel1, $channel2]);

        $this->beConstructedWith($customerRepository, $customerChannelsResolver, $activeCampaignContactClient, $listSubscriptionStatusUpdater);
    }

    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(ContactListsUpdaterHandler::class);
    }

    public function it_throws_if_customer_is_not_found(
        CustomerRepositoryInterface $customerRepository
    ): void {
        $customerRepository->find(12)->shouldBeCalledOnce()->willReturn(null);

        $this->shouldThrow(new InvalidArgumentException('Customer with id "12" does not exists.'))->during(
            '__invoke',
            [new ContactListsUpdater(12)]
        );
    }

    public function it_throws_if_customer_is_not_an_implementation_of_active_campaign_aware_interface(
        CustomerRepositoryInterface $customerRepository,
        SyliusCustomerInterface $syliusCustomer
    ): void {
        $customerRepository->find(12)->shouldBeCalledOnce()->willReturn($syliusCustomer);

        $this->shouldThrow(new InvalidArgumentException('The Customer entity should implement the "Webgriffe\SyliusActiveCampaignPlugin\Model\CustomerActiveCampaignAwareInterface" class.'))->during(
            '__invoke',
            [new ContactListsUpdater(12)]
        );
    }

    public function it_throws_if_customer_has_not_been_exported_to_active_campaign_yet(
        ActiveCampaignAwareInterface $customer
    ): void {
        $customer->getActiveCampaignId()->willReturn(null);

        $this->shouldThrow(new InvalidArgumentException('The Customer with id "12" does not have an ActiveCampaign id.'))->during(
            '__invoke',
            [new ContactListsUpdater(12)]
        );
    }

    public function it_throws_if_it_can_not_retrieves_the_contact_from_active_campaign(
        ActiveCampaignResourceClientInterface $activeCampaignContactClient,
        RetrieveResourceResponseInterface $retrieveResourceResponse2
    ): void {
        $activeCampaignContactClient->get(134)->willReturn($retrieveResourceResponse2);

        $this->shouldThrow(new LogicException('The retrieve contact response for the contact with id "134" from customer with id "12" is not valid.'))->during(
            '__invoke',
            [new ContactListsUpdater(12)]
        );
    }

    public function it_throws_if_the_contact_from_active_campaign_has_a_different_id_from_the_customer_given(
        ResourceResponseInterface $resourceResponse
    ): void {
        $resourceResponse->getId()->willReturn(4);

        $this->shouldThrow(new LogicException('The retrieved contact has id "4" that does not match with the contact id "134" used for search it.'))->during(
            '__invoke',
            [new ContactListsUpdater(12)]
        );
    }

    public function it_updates_list_status_for_every_channel(
        ListSubscriptionStatusUpdaterInterface $listSubscriptionStatusUpdater,
        CustomerInterface $customer,
        ChannelInterface $channel1,
        ChannelInterface $channel2
    ): void {
        $listSubscriptionStatusUpdater->update($customer, $channel1, ContactListInterface::SUBSCRIBED_STATUS_CODE)->shouldBeCalledOnce();
        $listSubscriptionStatusUpdater->update($customer, $channel2, ContactListInterface::UNSUBSCRIBED_STATUS_CODE)->shouldBeCalledOnce();

        $this->__invoke(new ContactListsUpdater(12));
    }
}
