<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\Enqueuer;

use Tests\Webgriffe\SyliusActiveCampaignPlugin\App\Entity\Channel\ChannelInterface;
use Prophecy\Argument;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Enqueuer\WebhookEnqueuer;
use PhpSpec\ObjectBehavior;
use Webgriffe\SyliusActiveCampaignPlugin\Enqueuer\WebhookEnqueuerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Generator\ChannelHostnameUrlGeneratorInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Webhook\WebhookCreate;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ListResourcesResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Webhook\WebhookResponse;
use Webmozart\Assert\InvalidArgumentException;

class WebhookEnqueuerSpec extends ObjectBehavior
{
    public function let(
        MessageBusInterface $messageBus,
        ActiveCampaignResourceClientInterface $activeCampaignWebhookClient,
        ChannelHostnameUrlGeneratorInterface $channelHostnameUrlGenerator,
        ChannelInterface $channel
    ): void {
        $channel->getId()->willReturn(3);
        $channel->getActiveCampaignListId()->willReturn(4);

        $channelHostnameUrlGenerator->generateForRoute($channel, 'webgriffe_sylius_active_campaign_list_status_webhook')->willReturn('https://localhost/webhook');

        $this->beConstructedWith($messageBus, $activeCampaignWebhookClient, $channelHostnameUrlGenerator);
    }

    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(WebhookEnqueuer::class);
    }

    public function it_implements_webhook_enqueuer_interface(): void
    {
        $this->shouldImplement(WebhookEnqueuerInterface::class);
    }

    public function it_throws_if_channel_id_is_null(ChannelInterface $channel): void
    {
        $channel->getId()->willReturn(null);
        $this->shouldThrow(new InvalidArgumentException('The channel id should not be null.'))
            ->during('enqueue', [$channel]);
    }

    public function it_throws_if_channel_list_id_is_null(ChannelInterface $channel): void
    {
        $channel->getActiveCampaignListId()->willReturn(null);
        $this->shouldThrow(new InvalidArgumentException('The channel ActiveCampaign list id should not be null.'))
            ->during('enqueue', [$channel]);
    }

    public function it_does_not_enqueue_a_webhook_creation_if_it_is_already_found_on_active_campaign(
        ChannelInterface $channel,
        MessageBusInterface $messageBus,
        ActiveCampaignResourceClientInterface $activeCampaignWebhookClient,
        ListResourcesResponseInterface $listWebhooksResponse
    ): void {
        $activeCampaignWebhookClient->list([
            'filters[url]' => 'https://localhost/webhook',
            'filters[listid]' => 4,
        ])->shouldBeCalledOnce()->willReturn($listWebhooksResponse);

        $listWebhooksResponse->getResourceResponseLists()->shouldBeCalledOnce()->willReturn([new WebhookResponse(3)]);

        $messageBus->dispatch(Argument::any())->shouldNotBeCalled();

        $this->enqueue($channel);
    }

    public function it_enqueues_a_webhook_creation_if_it_is_not_found_on_active_campaign(
        ChannelInterface $channel,
        MessageBusInterface $messageBus,
        ActiveCampaignResourceClientInterface $activeCampaignWebhookClient,
        ListResourcesResponseInterface $listWebhooksResponse
    ): void {
        $activeCampaignWebhookClient->list([
            'filters[url]' => 'https://localhost/webhook',
            'filters[listid]' => 4,
        ])->shouldBeCalledOnce()->willReturn($listWebhooksResponse);

        $listWebhooksResponse->getResourceResponseLists()->shouldBeCalledOnce()->willReturn([]);

        $messageBus->dispatch(Argument::type(WebhookCreate::class))->shouldBeCalledOnce()->willReturn(new Envelope(new WebhookCreate(3)));

        $this->enqueue($channel);
    }
}
