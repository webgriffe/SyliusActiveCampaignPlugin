<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\EcommerceCustomer;

use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceCustomer\EcommerceCustomerRemove;
use Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\EcommerceCustomer\EcommerceCustomerRemoveHandler;
use PhpSpec\ObjectBehavior;

class EcommerceCustomerRemoveHandlerSpec extends ObjectBehavior
{
    public function let(
        ActiveCampaignResourceClientInterface $activeCampaignContactClient,
    ): void {
        $this->beConstructedWith($activeCampaignContactClient);
    }

    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(EcommerceCustomerRemoveHandler::class);
    }

    public function it_removes_ecommerce_customer_on_active_campaign(
        ActiveCampaignResourceClientInterface $activeCampaignContactClient,
    ): void {
        $activeCampaignContactClient->remove(1234)->shouldBeCalledOnce();

        $this->__invoke(new EcommerceCustomerRemove(1234));
    }
}
