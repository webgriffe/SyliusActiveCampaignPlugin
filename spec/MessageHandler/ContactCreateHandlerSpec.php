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
use Webgriffe\SyliusActiveCampaignPlugin\Message\ContactCreate;
use Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\ContactCreateHandler;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ContactInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ContactResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\CreateContactResponse;

class ContactCreateHandlerSpec extends ObjectBehavior
{
    public function let(
        ContactMapperInterface $contactMapper,
        ContactInterface $contact,
        CustomerInterface $customer,
        ActiveCampaignClientInterface $activeCampaignClient,
        CustomerRepositoryInterface $customerRepository
    ): void {
        $contactMapper->mapFromCustomer($customer)->willReturn($contact);

        $customer->getActiveCampaignId()->willReturn(null);

        $customerRepository->find(12)->willReturn($customer);

        $this->beConstructedWith($contactMapper, $activeCampaignClient, $customerRepository);
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
            '__invoke', [new ContactCreate(12)]
        );
    }

    public function it_throws_if_customer_is_not_an_implementation_of_active_campaign_aware_interface(
        CustomerRepositoryInterface $customerRepository,
        SyliusCustomerInterface $syliusCustomer
    ): void {
        $customerRepository->find(12)->shouldBeCalledOnce()->willReturn($syliusCustomer);

        $this->shouldThrow(InvalidArgumentException::class)->during(
            '__invoke', [new ContactCreate(12)]
        );
    }

    public function it_throws_if_customer_has_been_already_exported_to_active_campaign(
        ActiveCampaignAwareInterface $customer
    ): void {
        $customer->getActiveCampaignId()->willReturn('321');

        $this->shouldThrow(InvalidArgumentException::class)->during(
            '__invoke', [new ContactCreate(12)]
        );
    }

    public function it_creates_contact_on_active_campaign(
        ContactInterface $contact,
        ActiveCampaignClientInterface $activeCampaignClient,
        CustomerInterface $customer,
        CustomerRepositoryInterface $customerRepository
    ): void {
        $activeCampaignClient->createContact($contact)->shouldBeCalledOnce()->willReturn(new CreateContactResponse([], new ContactResponse('info@activecampaign.com', 'today', 'today', '', [], 3423, '')));
        $customer->setActiveCampaignId(3423)->shouldBeCalledOnce();
        $customerRepository->add($customer)->shouldBeCalledOnce();

        $this->__invoke(new ContactCreate(12));
    }
}
