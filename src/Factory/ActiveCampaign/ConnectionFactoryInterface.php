<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign;

use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ConnectionInterface;

interface ConnectionFactoryInterface
{
    public function createNew(string $service, string $externalId, string $name): ConnectionInterface;
}
