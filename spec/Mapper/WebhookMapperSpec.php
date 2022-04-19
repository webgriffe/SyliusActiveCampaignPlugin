<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\Mapper;

use Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign\WebhookFactoryInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\WebhookMapper;
use PhpSpec\ObjectBehavior;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\WebhookMapperInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\WebhookInterface;

class WebhookMapperSpec extends ObjectBehavior
{
    public function let(WebhookFactoryInterface $webhookFactory, WebhookInterface $webhook): void
    {
        $webhookFactory->createNewFromNameAndUrl('event subscriber', 'https://localhost/webhook')->willReturn($webhook);

        $this->beConstructedWith($webhookFactory);
    }

    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(WebhookMapper::class);
    }

    public function it_implements_webhook_mapper_interface(): void
    {
        $this->shouldImplement(WebhookMapperInterface::class);
    }

    public function it_should_returns_webhook(WebhookInterface $webhook): void
    {
        $webhook->setEvents(['subscribe'])->shouldBeCalledOnce();
        $webhook->setSources(['admin'])->shouldBeCalledOnce();
        $webhook->setListId(4)->shouldBeCalledOnce();
        $this->map('event subscriber', 'https://localhost/webhook', ['subscribe'], ['admin'], 4)->shouldReturn($webhook);
    }
}
