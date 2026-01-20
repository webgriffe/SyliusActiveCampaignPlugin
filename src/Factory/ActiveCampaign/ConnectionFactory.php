<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign;

use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ConnectionInterface;

final class ConnectionFactory extends AbstractFactory implements ConnectionFactoryInterface
{
    #[\Override]
    public function createNew(string $service, string $externalId, string $name): ConnectionInterface
    {
        /** @var class-string<ConnectionInterface> $class */
        $class = $this->targetClassFQCN;

        $connection = new $class(
            $service,
            $externalId,
            $name,
        );

        return $connection;
    }
}
