<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\Mapper;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use stdClass;
use Sylius\Component\Core\Model\CustomerInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Exception\CustomerDoesNotHaveEmailException;
use Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign\ContactFactoryInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\ContactMapper;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\ContactMapperInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ContactInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\FieldValueInterface;
use Webmozart\Assert\InvalidArgumentException;

class ContactMapperSpec extends ObjectBehavior
{
    public function let(
        ContactFactoryInterface $contactFactory,
        EventDispatcherInterface $eventDispatcher,
        CustomerInterface $customer,
        ContactInterface $contact
    ): void {
        $customer->getFirstName()->willReturn('Samuel');
        $customer->getLastName()->willReturn('Jackson');
        $customer->getPhoneNumber()->willReturn('0324 213 231');
        $customer->getEmail()->willReturn('customer@domain.org');

        $contactFactory->createNewFromEmail('customer@domain.org')->willReturn($contact);

        $this->beConstructedWith($contactFactory, $eventDispatcher);
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

    public function it_throws_if_field_values_not_returns_an_array_of_field_value_interface(
        CustomerInterface $customer,
        ContactInterface $contact,
        EventDispatcherInterface $eventDispatcher,
        GenericEvent $event
    ): void {
        $contact->setFirstName('Samuel')->shouldBeCalledOnce();
        $contact->setLastName('Jackson')->shouldBeCalledOnce();
        $contact->setPhone('0324 213 231')->shouldBeCalledOnce();

        $eventDispatcher->dispatch(Argument::type(GenericEvent::class), 'webgriffe.sylius_active_campaign_plugin.mapper.customer.pre_add_field_values')->shouldBeCalledOnce()->willReturn($event);

        $event->getArgument('fieldValues')->shouldBeCalledOnce()->willReturn([new stdClass()]);

        $this->shouldThrow(new InvalidArgumentException('The field values should be an array of "Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\FieldValueInterface".'))->during(
            'mapFromCustomer',
            [$customer]
        );
    }

    public function it_returns_an_active_campaign_contact_mapped_by_customer(
        CustomerInterface $customer,
        ContactInterface $contact,
        EventDispatcherInterface $eventDispatcher,
        GenericEvent $event,
        FieldValueInterface $fieldValue
    ): void {
        $contact->setFirstName('Samuel')->shouldBeCalledOnce();
        $contact->setLastName('Jackson')->shouldBeCalledOnce();
        $contact->setPhone('0324 213 231')->shouldBeCalledOnce();

        $eventDispatcher->dispatch(Argument::type(GenericEvent::class), 'webgriffe.sylius_active_campaign_plugin.mapper.customer.pre_add_field_values')->shouldBeCalledOnce()->willReturn($event);

        $event->getArgument('fieldValues')->shouldBeCalledOnce()->willReturn([$fieldValue]);

        $contact->setFieldValues([$fieldValue])->shouldBeCalledOnce();

        $this->mapFromCustomer($customer)->shouldReturn($contact);
    }
}
