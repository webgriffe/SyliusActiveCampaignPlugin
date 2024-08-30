<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Enqueuer;

use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Generator\ChannelHostnameUrlGeneratorInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Webhook\WebhookCreate;
use Webmozart\Assert\Assert;

final class WebhookEnqueuer implements WebhookEnqueuerInterface
{
    public function __construct(
        private MessageBusInterface $messageBus,
        private ActiveCampaignResourceClientInterface $activeCampaignWebhookClient,
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

    public function enqueue($channel): void
    {
        /** @var string|int|null $channelId */
        $channelId = $channel->getId();
        Assert::notNull($channelId, 'The channel id should not be null.');
        $this->logger?->debug(sprintf(
            'Starting enqueuing webhook for channel "%s".',
            $channelId,
        ));
        $activeCampaignListId = $channel->getActiveCampaignListId();
        Assert::notNull($activeCampaignListId, 'The channel ActiveCampaign list id should not be null.');
        $searchWebhooks = $this->activeCampaignWebhookClient->list([
            'filters[url]' => $this->channelHostnameUrlGenerator->generateForRoute($channel, 'webgriffe_sylius_active_campaign_list_status_webhook'),
            'filters[listid]' => (string) $activeCampaignListId,
        ])->getResourceResponseLists();
        if (count($searchWebhooks) > 0) {
            $this->logger?->debug(sprintf(
                'Channel "%s" has an already valued ActiveCampaign webhook for list "%s", so we can skip the webhook creation.',
                $channelId,
                $activeCampaignListId,
            ));

            return;
        }
        $this->logger?->debug(sprintf(
            'Channel "%s" has no ActiveCampaign webhook for list "%s", so we have to create it.',
            $channelId,
            $activeCampaignListId,
        ));

        $this->messageBus->dispatch(new WebhookCreate($channelId));
    }
}
