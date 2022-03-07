<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\Mapper;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\CustomerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Exception\CustomerDoesNotHaveEmailException;
use Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaignContactFactoryInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\ContactMapper;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\ContactMapperInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignContact;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignContactInterface;

class ContactMapperSpec extends ObjectBehavior
{
    public function let(
        ActiveCampaignContactFactoryInterface $contactFactory,
        CustomerInterface $customer,
        ActiveCampaignContactInterface $contact
    ): void {
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
            'mapFromCustomer', [$customer]
        );
    }

    public function it_returns_an_instance_of_active_campaign_contact(CustomerInterface $customer,): void
    {
        $this->mapFromCustomer($customer)->shouldReturnAnInstanceOf(ActiveCampaignContactInterface::class);
    }
}
