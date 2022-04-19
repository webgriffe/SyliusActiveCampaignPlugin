<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign;

use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\WebhookInterface;

interface WebhookFactoryInterface
{
    public function createNewFromNameAndUrl(string $name, string $url): WebhookInterface;
}
