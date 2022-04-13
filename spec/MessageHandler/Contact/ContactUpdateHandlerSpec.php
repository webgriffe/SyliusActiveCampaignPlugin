<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\Contact;

use App\Entity\Customer\CustomerInterface;
use InvalidArgumentException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\CustomerInterface as SyliusCustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\ContactMapperInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Contact\ContactTagsAdder;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Contact\ContactUpdate;
use Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\Contact\ContactUpdateHandler;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ContactInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\UpdateResourceResponseInterface;

class ContactUpdateHandlerSpec extends ObjectBehavior
{
    public function let(
        ContactMapperInterface $contactMapper,
        ContactInterface $contact,
        CustomerInterface $customer,
        ActiveCampaignResourceClientInterface $activeCampaignContactClient,
        CustomerRepositoryInterface $customerRepository,
        MessageBusInterface $messageBus
    ): void {
        $contactMapper->mapFromCustomer($customer)->willReturn($contact);

        $customer->getActiveCampaignId()->willReturn('1234');

        $customerRepository->find(12)->willReturn($customer);

        $this->beConstructedWith($contactMapper, $activeCampaignContactClient, $customerRepository, $messageBus);
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
            '__invoke',
            [new ContactUpdate(12, 1234)]
        );
    }

    public function it_throws_if_customer_is_not_an_implementation_of_active_campaign_aware_interface(
        CustomerRepositoryInterface $customerRepository,
        SyliusCustomerInterface $syliusCustomer
    ): void {
        $customerRepository->find(12)->shouldBeCalledOnce()->willReturn($syliusCustomer);

        $this->shouldThrow(new InvalidArgumentException('The Customer entity should implement the "Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface" class.'))->during(
            '__invoke',
            [new ContactUpdate(12, 1234)]
        );
    }

    public function it_throws_if_customer_has_not_been_exported_to_active_campaign_yet(
        ActiveCampaignAwareInterface $customer
    ): void {
        $customer->getActiveCampaignId()->willReturn(null);

        $this->shouldThrow(new InvalidArgumentException('The Customer with id "12" has an ActiveCampaign id that does not match. Expected "1234", given "".'))->during(
            '__invoke',
            [new ContactUpdate(12, 1234)]
        );
    }

    public function it_throws_if_customer_has_a_different_id_on_active_campaign_than_the_message_provided(
        ActiveCampaignAwareInterface $customer
    ): void {
        $customer->getActiveCampaignId()->willReturn('321');

        $this->shouldThrow(new InvalidArgumentException('The Customer with id "12" has an ActiveCampaign id that does not match. Expected "1234", given "321".'))->during(
            '__invoke',
            [new ContactUpdate(12, 1234)]
        );
    }

    public function it_updates_contact_on_active_campaign(
        ContactInterface $contact,
        ActiveCampaignResourceClientInterface $activeCampaignContactClient,
        UpdateResourceResponseInterface $updateContactResponse,
        MessageBusInterface $messageBus
    ): void {
        $activeCampaignContactClient->update(1234, $contact)->shouldBeCalledOnce()->willReturn($updateContactResponse);
        $messageBus->dispatch(Argument::type(ContactTagsAdder::class))->shouldBeCalledOnce()->willReturn(new Envelope(new ContactTagsAdder(12)));

        $this->__invoke(new ContactUpdate(12, 1234));
    }
}
