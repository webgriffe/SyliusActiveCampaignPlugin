<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign;

use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ConnectionInterface;

final class ConnectionFactory extends AbstractFactory implements ConnectionFactoryInterface
{
    public function createNew(string $service, string $externalId, string $name): ConnectionInterface
    {
        /** @var ConnectionInterface $connection */
        $connection = new $this->targetClassFQCN(
            $service,
            $externalId,
            $name,
        );

        return $connection;
    }
}
