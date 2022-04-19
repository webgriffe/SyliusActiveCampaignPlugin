<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign;

use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\Connection;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ConnectionInterface;

final class ConnectionFactory implements ConnectionFactoryInterface
{
    public function createNew(string $service, string $externalId, string $name): ConnectionInterface
    {
        return new Connection(
            $service,
            $externalId,
            $name
        );
    }
}
