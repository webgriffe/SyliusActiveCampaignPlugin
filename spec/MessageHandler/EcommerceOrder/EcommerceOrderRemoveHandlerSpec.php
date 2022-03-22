<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\EcommerceOrder;

use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceOrder\EcommerceOrderRemove;
use Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\EcommerceOrder\EcommerceOrderRemoveHandler;
use PhpSpec\ObjectBehavior;

class EcommerceOrderRemoveHandlerSpec extends ObjectBehavior
{
    public function let(
        ActiveCampaignResourceClientInterface $activeCampaignEcommerceOrderClient,
    ): void {
        $this->beConstructedWith($activeCampaignEcommerceOrderClient);
    }

    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(EcommerceOrderRemoveHandler::class);
    }

    public function it_removes_ecommerce_order_on_active_campaign(
        ActiveCampaignResourceClientInterface $activeCampaignEcommerceOrderClient,
    ): void {
        $activeCampaignEcommerceOrderClient->remove(1)->shouldBeCalledOnce();

        $this->__invoke(new EcommerceOrderRemove(1));
    }
}
