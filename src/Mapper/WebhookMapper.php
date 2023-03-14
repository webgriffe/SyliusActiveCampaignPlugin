<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Mapper;

use Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign\WebhookFactoryInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\WebhookInterface;

final class WebhookMapper implements WebhookMapperInterface
{
    public function __construct(
        private WebhookFactoryInterface $webhookFactory,
    ) {
    }

    public function map(string $name, string $url, array $events = [], array $sources = [], ?int $listId = null): WebhookInterface
    {
        $webhook = $this->webhookFactory->createNewFromNameAndUrl($name, $url);
        $webhook->setEvents($events);
        $webhook->setSources($sources);
        $webhook->setListId($listId);

        return $webhook;
    }
}
