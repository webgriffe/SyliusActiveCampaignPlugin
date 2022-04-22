<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign;

use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ConnectionInterface;

final class ConnectionFactory implements ConnectionFactoryInterface
{
    public function __construct(
        private string $connectionFQCN
    ) {
    }

    public function createNew(string $service, string $externalId, string $name): ConnectionInterface
    {
        /** @var ConnectionInterface $connection */
        $connection = new $this->connectionFQCN(
            $service,
            $externalId,
            $name
        );

        return $connection;
    }
}
