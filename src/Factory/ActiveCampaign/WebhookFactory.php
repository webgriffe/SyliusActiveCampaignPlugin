<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign;

use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\WebhookInterface;

final class WebhookFactory implements WebhookFactoryInterface
{
    public function __construct(
        private string $webhookFQCN
    ) {
    }

    public function createNewFromNameAndUrl(string $name, string $url): WebhookInterface
    {
        /** @var WebhookInterface $webhook */
        $webhook = new $this->webhookFQCN(
            $name,
            $url
        );

        return $webhook;
    }
}
