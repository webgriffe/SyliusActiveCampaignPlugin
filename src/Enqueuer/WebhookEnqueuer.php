<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Enqueuer;

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
    ) {
    }

    public function enqueue($channel): void
    {
        /** @var string|int|null $channelId */
        $channelId = $channel->getId();
        Assert::notNull($channelId, 'The channel id should not be null.');
        $activeCampaignListId = $channel->getActiveCampaignListId();
        Assert::notNull($activeCampaignListId, 'The channel ActiveCampaign list id should not be null.');
        $searchWebhooks = $this->activeCampaignWebhookClient->list([
            'filters[url]' => $this->channelHostnameUrlGenerator->generate($channel, 'webgriffe_sylius_active_campaign_list_status_webhook'),
            'filters[listid]' => (string) $activeCampaignListId,
        ])->getResourceResponseLists();
        if (count($searchWebhooks) > 0) {
            return;
        }

        $this->messageBus->dispatch(new WebhookCreate($channelId));
    }
}
