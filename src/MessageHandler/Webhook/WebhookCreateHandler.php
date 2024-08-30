<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\Webhook;

use GuzzleHttp\Exception\GuzzleException;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Generator\ChannelHostnameUrlGeneratorInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\WebhookMapperInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Webhook\WebhookCreate;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ChannelActiveCampaignAwareInterface;

final class WebhookCreateHandler
{
    public function __construct(
        private WebhookMapperInterface $webhookMapper,
        private ActiveCampaignResourceClientInterface $activeCampaignWebhookClient,
        private ChannelRepositoryInterface $channelRepository,
        private ChannelHostnameUrlGeneratorInterface $channelHostnameUrlGenerator,
        private ?LoggerInterface $logger = null,
    ) {
        if ($this->logger === null) {
            trigger_deprecation(
                'webgriffe/sylius-active-campaign-plugin',
                'v0.12.2',
                'The logger argument is mandatory.',
            );
        }
    }

    /**
     * @throws \Throwable
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function __invoke(WebhookCreate $message): void
    {
        $channelId = $message->getChannelId();
        /** @var ChannelInterface|null $channel */
        $channel = $this->channelRepository->find($channelId);
        if ($channel === null) {
            throw new InvalidArgumentException(sprintf('Channel with id "%s" does not exists.', $channelId));
        }
        if (!$channel instanceof ChannelActiveCampaignAwareInterface) {
            throw new InvalidArgumentException(sprintf('The Channel entity should implement the "%s" class.', ChannelActiveCampaignAwareInterface::class));
        }
        $activeCampaignListId = $channel->getActiveCampaignListId();
        if ($activeCampaignListId === null) {
            throw new InvalidArgumentException(sprintf('The Channel with id "%s" does not have an ActiveCampaign list id.', $channelId));
        }
        try {
            $this->activeCampaignWebhookClient->create($this->webhookMapper->map(
                sprintf('Update Sylius newsletter subscription to list "%s"', $activeCampaignListId),
                $this->channelHostnameUrlGenerator->generateForRoute($channel, 'webgriffe_sylius_active_campaign_list_status_webhook'),
                ['subscribe', 'unsubscribe'],
                ['public', 'admin', 'system'],
                $activeCampaignListId,
            ));
        } catch (\Throwable $e) {
            $this->logger?->error($e->getMessage(), $e->getTrace());

            throw $e;
        }
    }
}
