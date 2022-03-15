<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\MessageHandler;

use PhpSpec\ObjectBehavior;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\ContactRemove;
use Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\ContactRemoveHandler;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\RemoveContactResponseInterface;

class ContactRemoveHandlerSpec extends ObjectBehavior
{
    public function let(
        ActiveCampaignClientInterface $activeCampaignClient
    ): void {
        $this->beConstructedWith($activeCampaignClient);
    }

    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(ContactRemoveHandler::class);
    }

    public function it_updates_contact_on_active_campaign(
        ActiveCampaignClientInterface $activeCampaignClient
    ): void {
        $activeCampaignClient->removeContact(1234)->shouldBeCalledOnce();

        $this->__invoke(new ContactRemove(1234));
    }
}
