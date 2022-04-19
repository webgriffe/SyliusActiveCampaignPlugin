<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Webhook;

use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\CreateResourceResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ResourceResponseInterface;

final class CreateWebhookResponse implements CreateResourceResponseInterface
{
    public function __construct(
        private WebhookResponse $webhook
    ) {
    }

    public function getResourceResponse(): ResourceResponseInterface
    {
        return $this->webhook;
    }
}
