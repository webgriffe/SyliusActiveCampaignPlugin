<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\Contact;

use PhpSpec\ObjectBehavior;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Contact\ContactRemove;
use Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\Contact\ContactRemoveHandler;

class ContactRemoveHandlerSpec extends ObjectBehavior
{
    public function let(
        ActiveCampaignResourceClientInterface $activeCampaignContactClient,
    ): void {
        $this->beConstructedWith($activeCampaignContactClient);
    }

    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(ContactRemoveHandler::class);
    }

    public function it_removes_contact_on_active_campaign(
        ActiveCampaignResourceClientInterface $activeCampaignContactClient,
    ): void {
        $activeCampaignContactClient->remove(1234)->shouldBeCalledOnce();

        $this->__invoke(new ContactRemove(1234));
    }
}
