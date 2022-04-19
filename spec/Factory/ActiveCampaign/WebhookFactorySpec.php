<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign;

use Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign\WebhookFactory;
use PhpSpec\ObjectBehavior;
use Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign\WebhookFactoryInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\Webhook;

class WebhookFactorySpec extends ObjectBehavior
{
    public function let(): void
    {
        $this->beConstructedWith();
    }

    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(WebhookFactory::class);
    }

    public function it_implements_webhook_factory_interface(): void
    {
        $this->shouldImplement(WebhookFactoryInterface::class);
    }

    public function it_should_returns_a_webhook_instance(): void
    {
        $this->createNewFromNameAndUrl(
            'subscribe to a list',
            'http://localhost/webhook'
        )->shouldReturnAnInstanceOf(Webhook::class);
    }
}
