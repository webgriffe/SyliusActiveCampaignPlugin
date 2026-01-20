<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Webhook;

use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ListResourcesResponseInterface;

/** @psalm-api */
final class ListWebhooksResponse implements ListResourcesResponseInterface
{
    /** @param WebhookResponse[] $webhooks */
    public function __construct(
        private array $webhooks,
    ) {
    }

    /** @return WebhookResponse[] */
    #[\Override]
    public function getResourceResponseLists(): array
    {
        return $this->webhooks;
    }
}
