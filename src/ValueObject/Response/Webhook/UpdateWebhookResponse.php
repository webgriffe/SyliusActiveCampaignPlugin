<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Webhook;

use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ResourceResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\UpdateResourceResponseInterface;

/** @psalm-api */
final class UpdateWebhookResponse implements UpdateResourceResponseInterface
{
    public function __construct(
        private WebhookResponse $webhook,
    ) {
    }

    #[\Override]
    public function getResourceResponse(): ResourceResponseInterface
    {
        return $this->webhook;
    }
}
