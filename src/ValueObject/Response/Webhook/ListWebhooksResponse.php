<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Webhook;

use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ListResourcesResponseInterface;

final class ListWebhooksResponse implements ListResourcesResponseInterface
{
    /** @param WebhookResponse[] $webhooks */
    public function __construct(
        private array $webhooks
    ) {
    }

    /** @return WebhookResponse[] */
    public function getResourceResponseLists(): array
    {
        return $this->webhooks;
    }
}
