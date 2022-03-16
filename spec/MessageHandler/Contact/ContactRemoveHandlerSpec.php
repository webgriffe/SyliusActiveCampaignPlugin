<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\Contact;

use PhpSpec\ObjectBehavior;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Contact\ContactRemove;
use Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\Contact\ContactRemoveHandler;
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
