<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Connection;

use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ListResourcesResponseInterface;

final class ListConnectionsResponse implements ListResourcesResponseInterface
{
    /**
     * @param ConnectionResponse[] $connections
     */
    public function __construct(
        private array $connections
    ) {
    }

    /** @return ConnectionResponse[] */
    public function getResourceResponseLists(): array
    {
        return $this->connections;
    }
}
