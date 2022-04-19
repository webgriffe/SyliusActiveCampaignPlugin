<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Mapper;

use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\WebhookInterface;

interface WebhookMapperInterface
{
    /**
     * @param string[] $events
     * @param string[] $sources
     */
    public function map(string $name, string $url, array $events = [], array $sources = [], ?int $listId = null): WebhookInterface;
}
