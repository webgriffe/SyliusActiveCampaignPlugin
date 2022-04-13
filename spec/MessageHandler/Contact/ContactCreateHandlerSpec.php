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
use Webgriffe\SyliusActiveCampaignPlugin\Message\Contact\ContactCreate;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Contact\ContactTagsAdder;
use Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\Contact\ContactCreateHandler;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ContactInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\CreateResourceResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ResourceResponseInterface;

class ContactCreateHandlerSpec extends ObjectBehavior
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

        $customer->getActiveCampaignId()->willReturn(null);

        $customerRepository->find(12)->willReturn($customer);

        $this->beConstructedWith($contactMapper, $activeCampaignContactClient, $customerRepository, $messageBus);
    }

    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(ContactCreateHandler::class);
    }

    public function it_throws_if_customer_is_not_found(
        CustomerRepositoryInterface $customerRepository
    ): void {
        $customerRepository->find(12)->shouldBeCalledOnce()->willReturn(null);

        $this->shouldThrow(InvalidArgumentException::class)->during(
            '__invoke',
            [new ContactCreate(12)]
        );
    }

    public function it_throws_if_customer_is_not_an_implementation_of_active_campaign_aware_interface(
        CustomerRepositoryInterface $customerRepository,
        SyliusCustomerInterface $syliusCustomer
    ): void {
        $customerRepository->find(12)->shouldBeCalledOnce()->willReturn($syliusCustomer);

        $this->shouldThrow(InvalidArgumentException::class)->during(
            '__invoke',
            [new ContactCreate(12)]
        );
    }

    public function it_throws_if_customer_has_been_already_exported_to_active_campaign(
        ActiveCampaignAwareInterface $customer
    ): void {
        $customer->getActiveCampaignId()->willReturn('321');

        $this->shouldThrow(InvalidArgumentException::class)->during(
            '__invoke',
            [new ContactCreate(12)]
        );
    }

    public function it_creates_contact_on_active_campaign(
        ContactInterface $contact,
        ActiveCampaignResourceClientInterface $activeCampaignContactClient,
        CustomerInterface $customer,
        CustomerRepositoryInterface $customerRepository,
        CreateResourceResponseInterface $createContactResponse,
        ResourceResponseInterface $contactResponse,
        MessageBusInterface $messageBus
    ): void {
        $contactResponse->getId()->willReturn(3423);
        $createContactResponse->getResourceResponse()->willReturn($contactResponse);
        $activeCampaignContactClient->create($contact)->shouldBeCalledOnce()->willReturn($createContactResponse);
        $customer->setActiveCampaignId(3423)->shouldBeCalledOnce();
        $customerRepository->add($customer)->shouldBeCalledOnce();
        $messageBus->dispatch(Argument::type(ContactTagsAdder::class))->shouldBeCalledOnce()->willReturn(new Envelope(new ContactTagsAdder(12)));

        $this->__invoke(new ContactCreate(12));
    }
}
