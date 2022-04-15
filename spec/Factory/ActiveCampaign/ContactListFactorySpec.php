<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign;

use Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign\ContactListFactory;
use Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign\ContactListFactoryInterface;
use PhpSpec\ObjectBehavior;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ContactList;

class ContactListFactorySpec extends ObjectBehavior
{
    public function let(): void
    {
        $this->beConstructedWith();
    }

    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(ContactListFactory::class);
    }

    public function it_implements_contact_list_factory_interface(): void
    {
        $this->shouldImplement(ContactListFactoryInterface::class);
    }

    public function it_should_returns_a_contact_tag_instance(): void
    {
        $this->createNew(5, 2, 3)->shouldReturnAnInstanceOf(ContactList::class);
    }
}
