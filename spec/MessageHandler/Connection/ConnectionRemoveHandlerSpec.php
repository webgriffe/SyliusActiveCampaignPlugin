<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\Connection;

use PhpSpec\ObjectBehavior;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Connection\ConnectionRemove;
use Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\Connection\ConnectionRemoveHandler;

class ConnectionRemoveHandlerSpec extends ObjectBehavior
{
    public function let(
        ActiveCampaignResourceClientInterface $activeCampaignConnectionClient,
    ): void {
        $this->beConstructedWith($activeCampaignConnectionClient);
    }

    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(ConnectionRemoveHandler::class);
    }

    public function it_removes_connection_on_active_campaign(
        ActiveCampaignResourceClientInterface $activeCampaignConnectionClient,
    ): void {
        $activeCampaignConnectionClient->remove(1)->shouldBeCalledOnce();

        $this->__invoke(new ConnectionRemove(1));
    }
}
