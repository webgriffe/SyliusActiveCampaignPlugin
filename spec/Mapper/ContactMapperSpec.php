<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\Mapper;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\CustomerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Exception\CustomerDoesNotHaveEmailException;
use Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign\ContactFactoryInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\ContactMapper;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\ContactMapperInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ContactInterface;

class ContactMapperSpec extends ObjectBehavior
{
    public function let(
        ContactFactoryInterface $contactFactory,
        CustomerInterface $customer,
        ContactInterface $contact
    ): void {
        $customer->getFirstName()->willReturn('Samuel');
        $customer->getLastName()->willReturn('Jackson');
        $customer->getPhoneNumber()->willReturn('0324 213 231');
        $customer->getEmail()->willReturn('customer@domain.org');

        $contactFactory->createNewFromEmail('customer@domain.org')->willReturn($contact);

        $this->beConstructedWith($contactFactory);
    }

    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(ContactMapper::class);
    }

    public function it_implements_contact_mapper_interface(): void
    {
        $this->shouldImplement(ContactMapperInterface::class);
    }

    public function it_throws_if_customer_does_not_have_email(CustomerInterface $customer): void
    {
        $customer->getId()->shouldBeCalledOnce()->willReturn(1);
        $customer->getEmail()->willReturn(null);

        $this->shouldThrow(CustomerDoesNotHaveEmailException::class)->during(
            'mapFromCustomer',
            [$customer]
        );
    }

    public function it_returns_an_instance_of_active_campaign_contact(CustomerInterface $customer): void
    {
        $this->mapFromCustomer($customer)->shouldReturnAnInstanceOf(ContactInterface::class);
    }

    public function it_returns_an_active_campaign_contact_mapped_by_customer(CustomerInterface $customer, ContactInterface $contact): void
    {
        $contact->setFirstName('Samuel')->shouldBeCalledOnce();
        $contact->setLastName('Jackson')->shouldBeCalledOnce();
        $contact->setPhone('0324 213 231')->shouldBeCalledOnce();
        $this->mapFromCustomer($customer)->shouldReturn($contact);
    }
}
