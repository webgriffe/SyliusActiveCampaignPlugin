<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Connection;

use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\CreateResourceResponseInterface;

final class CreateConnectionResponse implements CreateResourceResponseInterface
{
    public function __construct(
        private CreateConnectionConnectionResponse $connection
    ) {
    }

    public function getConnection(): CreateConnectionConnectionResponse
    {
        return $this->connection;
    }
}
