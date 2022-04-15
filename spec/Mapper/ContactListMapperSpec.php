<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\Mapper;

use Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign\ContactListFactoryInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\ContactListMapper;
use PhpSpec\ObjectBehavior;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\ContactListMapperInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ContactListInterface;

class ContactListMapperSpec extends ObjectBehavior
{
    public function let(ContactListFactoryInterface $contactListFactory, ContactListInterface $contactList): void
    {
        $contactListFactory->createNew(3, 5, 7)->willReturn($contactList);

        $this->beConstructedWith($contactListFactory);
    }

    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(ContactListMapper::class);
    }

    public function it_implements_contact_list_mapper_interface(): void
    {
        $this->shouldImplement(ContactListMapperInterface::class);
    }

    public function it_should_returns_tag(ContactListInterface $contactList): void
    {
        $contactList->setSourceId(4)->shouldBeCalledOnce();

        $this->mapFromListContactStatusAndSourceId(3, 5, 7, 4)->shouldReturn($contactList);
    }
}
