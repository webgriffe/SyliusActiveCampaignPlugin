<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\Factory;

use Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaignContactFactory;
use PhpSpec\ObjectBehavior;
use Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaignContactFactoryInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignContact;

class ActiveCampaignContactFactorySpec extends ObjectBehavior
{
    public function let(): void
    {
        $this->beConstructedWith();
    }

    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(ActiveCampaignContactFactory::class);
    }

    public function it_implements_active_campaign_contact_factory_interface(): void
    {
        $this->shouldImplement(ActiveCampaignContactFactoryInterface::class);
    }

    public function it_should_returns_an_active_campaign_contact_instance(): void
    {
        $this->createNewFromEmail('info@domain.org')->shouldReturnAnInstanceOf(ActiveCampaignContact::class);
    }

    public function it_should_returns_an_active_campaign_contact_with_email(): void
    {
        $this->createNewFromEmail('info@domain.org')->getEmail()->shouldReturn('info@domain.org');
    }
}
