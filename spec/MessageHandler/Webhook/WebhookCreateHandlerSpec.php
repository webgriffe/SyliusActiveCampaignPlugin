<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\Webhook;

use Tests\Webgriffe\SyliusActiveCampaignPlugin\App\Entity\Channel\ChannelInterface;
use InvalidArgumentException;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface as SyliusChannelInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Generator\ChannelHostnameUrlGeneratorInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\WebhookMapperInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Webhook\WebhookCreate;
use Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\Webhook\WebhookCreateHandler;
use PhpSpec\ObjectBehavior;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\WebhookInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface;

class WebhookCreateHandlerSpec extends ObjectBehavior
{
    public function let(
        WebhookMapperInterface $webhookMapper,
        ActiveCampaignResourceClientInterface $activeCampaignWebhookClient,
        ChannelRepositoryInterface $channelRepository,
        ChannelInterface $channel,
        ChannelHostnameUrlGeneratorInterface $channelHostnameUrlGenerator
    ): void {
        $channel->getId()->willReturn(1);
        $channel->getActiveCampaignListId()->willReturn(4);

        $channelHostnameUrlGenerator->generateForRoute($channel, 'webgriffe_sylius_active_campaign_list_status_webhook')->willReturn('https://localhost/webhook');

        $channelRepository->find(1)->willReturn($channel);


        $this->beConstructedWith($webhookMapper, $activeCampaignWebhookClient, $channelRepository, $channelHostnameUrlGenerator);
    }

    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(WebhookCreateHandler::class);
    }

    public function it_throws_if_channel_is_not_found(
        ChannelRepositoryInterface $channelRepository
    ): void {
        $channelRepository->find(1)->shouldBeCalledOnce()->willReturn(null);

        $this->shouldThrow(new InvalidArgumentException('Channel with id "1" does not exists.'))->during(
            '__invoke',
            [new WebhookCreate(1)]
        );
    }

    public function it_throws_if_channel_is_not_an_implementation_of_active_campaign_aware_interface(
        ChannelRepositoryInterface $channelRepository,
        SyliusChannelInterface $syliusChannel
    ): void {
        $channelRepository->find(1)->shouldBeCalledOnce()->willReturn($syliusChannel);

        $this->shouldThrow(new InvalidArgumentException('The Channel entity should implement the "Webgriffe\SyliusActiveCampaignPlugin\Model\ChannelActiveCampaignAwareInterface" class.'))->during(
            '__invoke',
            [new WebhookCreate(1)]
        );
    }

    public function it_throws_if_channel_list_id_is_null(
        ActiveCampaignAwareInterface $channel
    ): void {
        $channel->getActiveCampaignListId()->willReturn(null);

        $this->shouldThrow(new InvalidArgumentException('The Channel with id "1" does not have an ActiveCampaign list id.'))->during(
            '__invoke',
            [new WebhookCreate(1)]
        );
    }

    public function it_creates_webhook(
        WebhookMapperInterface $webhookMapper,
        ActiveCampaignResourceClientInterface $activeCampaignWebhookClient,
        WebhookInterface $webhook
    ): void {
        $webhookMapper->map(
            'Update Sylius newsletter subscription to list "4"',
            'https://localhost/webhook',
            ['subscribe', 'unsubscribe'],
            ['public', 'admin', 'system'],
            4
        )->shouldBeCalledOnce()->willReturn($webhook);

        $activeCampaignWebhookClient->create($webhook)->shouldBeCalledOnce();

        $this->__invoke(new WebhookCreate(1));
    }
}
