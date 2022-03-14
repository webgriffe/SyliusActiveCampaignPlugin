<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\MessageHandler;

use App\Entity\Customer\CustomerInterface;
use InvalidArgumentException;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\CustomerInterface as SyliusCustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\ContactMapperInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\ContactUpdate;
use Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\ContactUpdateHandler;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ContactInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\UpdateContactResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface;

class ContactUpdateHandlerSpec extends ObjectBehavior
{
    public function let(
        ContactMapperInterface $contactMapper,
        ContactInterface $contact,
        CustomerInterface $customer,
        ActiveCampaignClientInterface $activeCampaignClient,
        CustomerRepositoryInterface $customerRepository
    ): void {
        $contactMapper->mapFromCustomer($customer)->willReturn($contact);

        $customer->getActiveCampaignId()->willReturn('1234');

        $customerRepository->find(12)->willReturn($customer);

        $this->beConstructedWith($contactMapper, $activeCampaignClient, $customerRepository);
    }

    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(ContactUpdateHandler::class);
    }

    public function it_throws_if_customer_is_not_found(
        CustomerRepositoryInterface $customerRepository
    ): void {
        $customerRepository->find(12)->shouldBeCalledOnce()->willReturn(null);

        $this->shouldThrow(new InvalidArgumentException('Customer with id "12" does not exists.'))->during(
            '__invoke', [new ContactUpdate(12, 1234)]
        );
    }

    public function it_throws_if_customer_is_not_an_implementation_of_active_campaign_aware_interface(
        CustomerRepositoryInterface $customerRepository,
        SyliusCustomerInterface $syliusCustomer
    ): void {
        $customerRepository->find(12)->shouldBeCalledOnce()->willReturn($syliusCustomer);

        $this->shouldThrow(new InvalidArgumentException('The Customer entity should implement the "Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface" class.'))->during(
            '__invoke', [new ContactUpdate(12, 1234)]
        );
    }

    public function it_throws_if_customer_has_has_a_different_id_on_active_campaign_than_the_message_provided(
        ActiveCampaignAwareInterface $customer
    ): void {
        $customer->getActiveCampaignId()->willReturn('321');

        $this->shouldThrow(new InvalidArgumentException('The customer with id "12" has an ActiveCampaign id that doe not match. Expected "1234", given "321".'))->during(
            '__invoke', [new ContactUpdate(12, 1234)]
        );
    }

    public function it_updates_contact_on_active_campaign(
        ContactInterface $contact,
        ActiveCampaignClientInterface $activeCampaignClient,
        UpdateContactResponseInterface $updateContactResponse
    ): void {
        $activeCampaignClient->updateContact(1234, $contact)->shouldBeCalledOnce()->willReturn($updateContactResponse);

        $this->__invoke(new ContactUpdate(12, 1234));
    }
}
