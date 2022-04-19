<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign;

use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\Webhook;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\WebhookInterface;

final class WebhookFactory implements WebhookFactoryInterface
{
    public function createNewFromNameAndUrl(string $name, string $url): WebhookInterface
    {
        return new Webhook(
            $name,
            $url
        );
    }
}
