<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign;

use PhpSpec\ObjectBehavior;
use Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign\ContactFactory;
use Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign\ContactFactoryInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\Contact;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ContactInterface;

class ContactFactorySpec extends ObjectBehavior
{
    public function let(): void
    {
        $this->beConstructedWith(Contact::class);
    }

    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(ContactFactory::class);
    }

    public function it_implements_active_campaign_contact_factory_interface(): void
    {
        $this->shouldImplement(ContactFactoryInterface::class);
    }

    public function it_should_returns_an_active_campaign_contact_instance(): void
    {
        $this->createNewFromEmail('info@domain.org')->shouldReturnAnInstanceOf(ContactInterface::class);
    }

    public function it_should_returns_an_active_campaign_contact_with_email(): void
    {
        $this->createNewFromEmail('info@domain.org')->getEmail()->shouldReturn('info@domain.org');
    }
}
