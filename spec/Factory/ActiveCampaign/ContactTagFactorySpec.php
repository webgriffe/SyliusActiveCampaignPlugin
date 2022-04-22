<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign;

use Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign\ContactTagFactory;
use PhpSpec\ObjectBehavior;
use Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign\ContactTagFactoryInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ContactTag;

class ContactTagFactorySpec extends ObjectBehavior
{
    public function let(): void
    {
        $this->beConstructedWith(ContactTag::class);
    }

    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(ContactTagFactory::class);
    }

    public function it_implements_contact_tag_factory_interface(): void
    {
        $this->shouldImplement(ContactTagFactoryInterface::class);
    }

    public function it_should_returns_a_contact_tag_instance(): void
    {
        $this->createNew(2, 3)->shouldReturnAnInstanceOf(ContactTag::class);
    }
}
